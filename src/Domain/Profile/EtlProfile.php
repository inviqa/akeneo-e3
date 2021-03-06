<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Profile;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EtlProfile implements LoadProfile, TransformProfile, ExtractProfile
{
    private bool $isDryRun;

    private array $conditions;

    private array $actions;

    /**
     * @var string[]
     */
    private array $dryRunCodes = [];

    public function __construct(array $data)
    {
        $data = self::resolve($data);

        $this->isDryRun = ($data['upload-type'] ?? '') === 'dry-run';
        $this->conditions = $data['conditions'] ?? [];
        $this->actions = $data['actions'] ?? [];
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }

    public function setDryRun(bool $value): void
    {
        $this->isDryRun = $value;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getDryRunCodes(): array
    {
        return $this->dryRunCodes;
    }

    public function setDryRunCodes(array $codes): void
    {
        $this->dryRunCodes = $codes;
    }

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefault('upload-type', 'api')
            ->setAllowedValues('upload-type', ['api', 'dry-run'])
            ->setDefault('conditions', function (OptionsResolver $conditionResolver) {
                $conditionResolver
                    ->setPrototype(true)
                    ->setRequired('field')
                    ->setDefined(['operator', 'value'])
                    ->setDefault('operator', '=')
                    ->setAllowedTypes('field', 'string')
                    ->setAllowedTypes('operator', 'string');
            })
            ->setRequired('actions')
            ->setAllowedTypes('actions', 'array');

        foreach ($data['actions'] ?? [] as $actionId => $action) {
            if (isset($action['type']) === false) {
                throw new LogicException(sprintf('No type specified for action %s', $actionId));
            }
        }

        return $resolver->resolve($data);
    }

    public function getBatchSize(): int
    {
        return 100;
    }
}
