<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class LoadProfile
{
    private bool $isDryRun;

    private function __construct(array $data)
    {
        $this->isDryRun = ($data['type'] ?? '') === 'dry-run';
    }

    public static function fromArray(array $data): self
    {
        $data = self::resolve($data);

        return new self($data);
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefined('type')
            ->setAllowedTypes('type', 'string');

        return $resolver->resolve($data);
    }
}
