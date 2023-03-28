<?php

namespace App\Module;

class Rsa
{
    /**
     * 解析字符串
     * @param $string
     * @return array
     */
    public static function parseStr($string)
    {
        $array = [
            'encrypt_version' => '',
            'client_public_key' => '',
        ];
        $string = str_replace(";", "&", $string);
        parse_str($string, $params);
        foreach ($params as $key => $value) {
            $key = strtolower(trim($key));
            if ($key) {
                $array[$key] = trim($value);
            }
        }
        if ($array['client_public_key']) {
            $array['client_public_key'] = chunk_split(url_safe($array['client_public_key'], false), 64);
            $array['client_public_key'] = "-----BEGIN PUBLIC KEY-----\n" . $array['client_public_key'] . "-----END PUBLIC KEY-----";
        }
        return $array;
    }

    /**
     * 生成密钥
     * @param $keySize
     * @return void
     */
    public static function load($keySize = 1024)
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
    }

    /**
     * 获取公钥
     * @return false|string
     */
    public static function getPublicKey()
    {
        return file_get_contents(config_path("RSA_PUBLIC"));
    }

    /**
     * 获取私钥
     * @return false|string
     */
    public static function getPrivateKey()
    {
        return file_get_contents(config_path("RSA_PRIVATE"));
    }

    /**
     * 加密
     * @param $data
     * @param $publicKey
     * @return mixed|string
     */
    public static function encrypt($data, $publicKey = null)
    {
        if ($publicKey === null) {
            $publicKey = self::getPublicKey();
        }
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::encrypt($v, $publicKey);
            }
        } elseif ($data) {
            $subEncrypted = "";
            openssl_public_encrypt($data, $subEncrypted, $publicKey);
            return url_safe(base64_encode($subEncrypted));
        }
        return $data;
    }

    /**
     * 解密
     * @param $data
     * @param $privateKey
     * @return mixed|string
     */
    public static function decrypt($data, $privateKey = null)
    {
        if ($privateKey === null) {
            $privateKey = self::getPrivateKey();
        }
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::decrypt($v, $privateKey);
            }
        } elseif (is_string($data)) {
            $subDecrypted = "";
            openssl_private_decrypt(base64_decode(url_safe($data, false)), $subDecrypted, $privateKey);
            return $subDecrypted;
        }
        return $data;
    }

    /**
     * 加密接口数据
     * @param $value
     * @param null $publicKey
     * @return string
     */
    public static function encryptApiData($value, $publicKey = null)
    {
        $data = base64_encode(Base::array2json($value));
        return self::encrypt(str_split($data, 117), $publicKey);
    }

    /**
     * 解密接口数据
     * @param $value
     * @param null $privateKey
     * @return array|false|mixed|string
     */
    public static function decryptApiData($value, $privateKey = null)
    {
        $data = self::decrypt($value, $privateKey);
        if (is_array($data)) {
            $data = implode($data);
        }
        return Base::json2array(base64_decode($data));
    }
}
