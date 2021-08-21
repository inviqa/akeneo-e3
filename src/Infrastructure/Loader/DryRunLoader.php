<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Loader;

use AkeneoE3\Domain\Loader;
use AkeneoE3\Domain\Resource\Resource;

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
