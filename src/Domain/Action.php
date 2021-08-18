<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

use AkeneoEtl\Domain\Resource\Resource;

interface Action
{
    public function execute(Resource $resource): void;
}
