<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource;

final class DryRunLoader implements Loader
{
    public function queue(Resource $resource): void
    {
        // dry run
    }

    public function load(): void
    {
        // dry run
    }
}
