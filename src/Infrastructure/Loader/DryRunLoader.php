<?php

namespace AkeneoEtl\Infrastructure\Loader;

use AkeneoEtl\Domain\Loader;

class DryRunLoader implements Loader
{
    public function queue(array $item): void
    {
        // dry run
    }

    public function load(): void
    {
        // dry run
    }
}
