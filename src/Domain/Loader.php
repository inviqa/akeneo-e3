<?php

namespace AkeneoEtl\Domain;

interface Loader
{
    public function addToBatch(array $item, bool $flush = false): void;

    public function flushBatch(): void;
}
