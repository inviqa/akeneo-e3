<?php

namespace AkeneoEtl\Infrastructure\Loader;

use AkeneoEtl\Domain\Loader;

class DryRunLoader implements Loader
{
    public function addToBatch(array $item, bool $flush = false): void
    {
        //var_dump($item);
    }

    public function flushBatch(): void
    {
        // dry run
    }
}
