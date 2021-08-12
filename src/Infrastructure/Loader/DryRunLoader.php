<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Loader;

use AkeneoEtl\Domain\Loader;
use AkeneoEtl\Domain\Resource\Resource;

final class DryRunLoader implements Loader
{
    public function load(Resource $resource): array
    {
        return [];
    }

    public function finish(): array
    {
        return [];
    }
}
