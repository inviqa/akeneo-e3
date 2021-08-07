<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Loader
{
    public function load(Resource $resource): void;

    /**
     * Finish loading, e.g. load remaining resources in bulk
     */
    public function finish(): void;
}
