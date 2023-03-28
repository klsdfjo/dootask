<?php

namespace App\Module;

class Rsa
{
    private static $rsa;
    private string $privateKey;
    private string $publicKey;

    public function __construct($keySize = 1024)
    {
        $priPath = config_path("RSA_PRIVATE");
        $pubPath = config_path("RSA_PUBLIC");
        if (!file_exists($priPath) || !file_exists($pubPath)) {
            $res = openssl_pkey_new([
                "private_key_bits" => $keySize,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            ]);
            openssl_pkey_export($res, $privateKey);
            $publicKey = openssl_pkey_get_details($res)["key"];
            file_put_contents($priPath, $privateKey);
            file_put_contents($pubPath, $publicKey);
        }
        $this->privateKey = file_get_contents($priPath);
        $this->publicKey = file_get_contents($pubPath);
        return $this;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function decrypt($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $this->decrypt($v);
            }
        } elseif (is_string($data)) {
            $subDecrypted = "";
            openssl_private_decrypt(base64_decode($data), $subDecrypted, $this->privateKey);
            return $subDecrypted;
        }
        return $data;
    }

    public static function load()
    {
        self::$rsa = new self();
    }

    public static function rsa()
    {
        if (self::$rsa == null) {
            self::load();
        }
        return self::$rsa;
    }

    /**
     * 解密接口数据
     * @param $value
     * @return array|false|mixed|string
     */
    public static function decryptApiData($value)
    {
        $data = self::rsa()->decrypt($value);
        if (is_array($data)) {
            $data = implode($data);
        }
        return Base::json2array(urldecode($data));
    }
}
