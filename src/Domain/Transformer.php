<?php

namespace AkeneoEtl\Domain;

interface Transformer
{
    public function transform(array $item): ?array;
}
