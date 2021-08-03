<?php

namespace AkeneoEtl\Domain\Profile;

class ConnectionProfile
{
    private string $host;
    private string $clientId;
    private string $clientSecret;
    private string $userName;
    private string $userPassword;

    private function __construct(
        string $host,
        string $clientId,
        string $clientSecret,
        string $userName,
        string $userPassword
    ) {
        $this->host = $host;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->userName = $userName;
        $this->userPassword = $userPassword;
    }
    public static function fromUser(
        string $host,
        string $clientId,
        string $clientSecret,
        string $userName,
        string $userPassword
    ): self {
        return new self($host, $clientId, $clientSecret, $userName, $userPassword);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserPassword(): string
    {
        return $this->userPassword;
    }
}
