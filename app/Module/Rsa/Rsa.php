<?php

namespace App\Module\Rsa;

class Rsa
{
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
        $pri_path = config_path("RSA_PRIVATE");
        $pub_path = config_path("RSA_PUBLIC");
        if (!file_exists($pri_path) || !file_exists($pub_path)) {
            System::shot("openssl genrsa -out {$pri_path} 1024");
            System::shot("openssl rsa -pubout -in {$pri_path} -out {$pub_path}");
        }
        $this->privateKey = file_get_contents($pri_path);
        $this->publicKey = file_get_contents($pub_path);
        return $this;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
