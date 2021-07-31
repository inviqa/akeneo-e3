<?php

namespace AkeneoEtl\Domain;

interface TransformerStep
{
    public function getType(): string;

    public function transform(array $item): ?array;
}
