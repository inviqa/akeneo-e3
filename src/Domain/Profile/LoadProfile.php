<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class LoadProfile
{
    public const MODE_UPDATE = 'update';
    public const MODE_CREATE = 'create';

    private bool $isDryRun;

    private string $mode;

    private function __construct(array $data)
    {
        $data = self::resolve($data);

        $this->isDryRun = ($data['type'] ?? '') === 'dry-run';
        $this->mode = $data['mode'];
    }

    public static function fromArray(array $data): self
    {
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
            ->setAllowedTypes('type', 'string')
            ->setDefault('mode', self::MODE_UPDATE)
            ->setAllowedValues('mode', [self::MODE_UPDATE, self::MODE_CREATE])
        ;

        return $resolver->resolve($data);
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}
