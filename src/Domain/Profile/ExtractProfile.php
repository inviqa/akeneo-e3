<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExtractProfile
{
    private array $conditions;

    private function __construct(array $data)
    {
        $data = self::validate($data);

        $this->conditions = $data['conditions'] ?? [];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    private static function validate(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver->setDefault('conditions', function (OptionsResolver $conditionResolver) {
            $conditionResolver
                ->setPrototype(true)
                ->setRequired(['field', 'operator'])
                ->setDefined('value')
                ->setAllowedTypes('field', 'string')
                ->setAllowedTypes('operator', 'string');
        });

        return $resolver->resolve($data);
    }
}
