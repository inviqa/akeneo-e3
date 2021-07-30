<?php

namespace App\Domain;

class ConnectionProfile
{
    public string $host;

    public string $clientId;

    public string $clientSecret;

    public string $userName;

    public string $userPassword;

    public static function fromUserNameAndPassword(
        string $host,
        string $clientId,
        string $clientSecret,
        string $userName,
        string $userPassword
    ): self {
        $profile = new self();

        $profile->host = $host;
        $profile->clientId = $clientId;
        $profile->clientSecret = $clientSecret;
        $profile->userName = $userName;
        $profile->userPassword = $userPassword;

        return $profile;
    }
}
