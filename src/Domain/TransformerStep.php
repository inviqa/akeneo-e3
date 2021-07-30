<?php

namespace App\Domain;

interface TransformerStep
{
    public function getName(): string;

    public function transform(array $item): array;
}
