<?php

namespace AkeneoEtl\Domain;

use phpDocumentor\Reflection\Types\Array_;

class EtlTransformProfile
{
    /**
     * @var iterable|Action[]
     */
    private iterable $actions;

    private function __construct(iterable $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param iterable|Action[] $actions
     */
    public static function fromActions(iterable $actions): self
    {
        return new self($actions);
    }

    /**
     * @return iterable|Action[]
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }
}
