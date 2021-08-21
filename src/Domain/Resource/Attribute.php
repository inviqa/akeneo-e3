<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Resource;

final class Attribute implements Field
{
    private string $name;
    private ?string $scope;
    private ?string $locale;

    private function __construct(string $name, ?string $scope, ?string $locale)
    {
        $this->name = $name;
        $this->scope = $scope;
        $this->locale = $locale;
    }

    public static function create(string $name, ?string $scope, ?string $locale): self
    {
        return new self($name, $scope, $locale);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
