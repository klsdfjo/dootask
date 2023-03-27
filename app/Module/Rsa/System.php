<?php

namespace App\Module\Rsa;

use Exception;
use function exec;

class System
{
    /**
     * @param $command
     * @return array
     * @throws Exception
     */
    public static function exec($command): array
    {
        exec($command, $output, $ret);
        if ($ret) {
            $output = implode("\n", $output);
            throw new Exception("System call failed.\nCommand: {$command}\n\n{$output}");
        }

        return $output;
    }

    /**
     * @param $command
     * @param int $retryNum
     * @return string|null
     */
    public static function shot($command, $retryNum = 5): ?string
    {
        try {
            $ret = self::exec($command);
        } catch (Exception $errA) {
            // 服务重启，循环5次，每次等待5秒后重新获取
            if ($retryNum > 0) {
                for ($i = 0; $i < $retryNum; $i++) {
                    sleep(3);
                    try {
                        $ret = self::exec($command);
                        break;
                    } catch (Exception $errB) {
                        if ($i == 4) {
                            info($errB->getMessage());
                        }
                    }
                }
            } else {
                info($errA->getMessage());
            }
        }
        return $ret[0] ?? null;
    }

    /**
     * @param $command
     * @param $appendLog
     * @return string|null
     */
    public static function cmd($command, $appendLog = false): ?string
    {
        if ($appendLog === true) {
            info($command);
        }
        return self::shot($command, 1);
    }

    /**
     * Check if a given ip is in a network
     * @param string $ip IP to check in IPV4 format eg. 127.0.0.1
     * @param string $range IP/CIDR netmask eg. 127.0.0.0/24, also 127.0.0.1 is accepted and /32 assumed
     * @return boolean true if the ip is in this range / false if not.
     */
    public static function ip_in_range(string $ip, string $range): bool
    {
        preg_match("/(\d{1,3}\.\d{1,3}\.\d{1,3})\.(\d{1,3})/", $ip, $match1);
        preg_match("/(\d{1,3}\.\d{1,3}\.\d{1,3})\.(\d{1,3})/", $range, $match2);
        if (empty($match1[0]) || empty($match2[0])) {
            return false;
        }
        if ($match1[1] != $match2[1]) {
            return false;
        }
        if (intval($match1[2]) < 2) {
            return false;
        }
        if (intval($match1[2]) > 255) {
            return false;
        }
        return true;
    }
}
