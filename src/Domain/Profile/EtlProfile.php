<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EtlProfile implements LoadProfile, TransformProfile, ExtractProfile
{
    public const MODE_UPDATE = 'update';
    public const MODE_DUPLICATE = 'duplicate';

    private bool $isDryRun;

    private string $mode;

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
        $this->mode = $data['upload-mode'] ?? self::MODE_UPDATE;
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

    public function getUploadMode(): string
    {
        return $this->mode;
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

    public function setDryRunCodes(array $codes): self
    {
        $this->dryRunCodes = $codes;

        if (count($codes) > 0) {
            $this->isDryRun = true;
        }

        return $this;
    }

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setDefault('upload-mode', self::MODE_UPDATE)
            ->setAllowedValues('upload-mode', [self::MODE_UPDATE, self::MODE_DUPLICATE])
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
}
