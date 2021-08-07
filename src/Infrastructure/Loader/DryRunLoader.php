<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource;

final class DryRunLoader implements Loader
{
    public function load(Resource $resource): void
    {
        // dry run
    }

    public function finish(): void
    {
        // dry run
    }
}
