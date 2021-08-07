<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use AkeneoEtl\Domain\Action;

class TransformProfile
{
    private array $actions;

    private function __construct(array $data)
    {
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
}
