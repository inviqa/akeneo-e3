<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Profile;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConnectionProfile
{
    private string $host;
    private string $clientId;
    private string $clientSecret;
    private string $userName;
    private string $userPassword;

    public function __construct(
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

    public static function fromArray(array $data): self
    {
        $data = self::resolve($data);

        return new self(
            $data['host'],
            $data['clientId'],
            $data['clientSecret'],
            $data['userName'],
            $data['userPassword']
        );
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

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired(['host', 'clientId', 'clientSecret', 'userName', 'userPassword'])
            ->setAllowedTypes('host', 'string')
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('userName', 'string')
            ->setAllowedTypes('userPassword', 'string');

        return $resolver->resolve($data);
    }
}
