<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Loader
{
    /**
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    public function load(Resource $resource): void;

    /**
     * Finish loading, e.g. load remaining resources in bulk.
     *
     * @throws \AkeneoEtl\Domain\Exception\LoadException
     */
    public function finish(): void;
}
