<?php

namespace AkeneoEtl\Domain;

use phpDocumentor\Reflection\Types\Array_;

class EtlTransformProfile
{
    /**
     * @var iterable|TransformerStep[]
     */
    private iterable $actions;

    private function __construct(iterable $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @param iterable|TransformerStep[] $actions
     */
    public static function fromActions(iterable $actions): self
    {
        return new self($actions);
    }

    /**
     * @return iterable|TransformerStep[]
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }
}
