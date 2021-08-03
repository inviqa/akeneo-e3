<?php

namespace AkeneoEtl\Domain;

class Field
{
    private string $name;
    private ?string $scope;
    private ?string $locale;

    public function __construct(string $name, ?string $scope, ?string $locale)
    {
        $this->name = $name;
        $this->scope = $scope;
        $this->locale = $locale;
    }

    public static function create(string $name, array $options): self
    {
        return new self($name, $options['scope'] ?? null, $options['locale'] ?? null);
    }

    public static function fromOptions(array $options): self
    {
        return new self($options['field'] ?? '', $options['scope'] ?? null, $options['locale'] ?? null);
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
