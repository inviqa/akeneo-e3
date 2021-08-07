<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TransformProfile
{
    private array $actions;

    private function __construct(array $data)
    {
        $data = self::resolve($data);

        $this->actions = $data['actions'] ?? [];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('actions')
            ->setAllowedTypes('actions', 'array');

        foreach ($data['actions'] as $actionId => $action) {
            if (isset($action['type']) === false) {
                throw new LogicException(sprintf('No type specified for action %s', $actionId));
            }
        }

        return $data;
    }
}
