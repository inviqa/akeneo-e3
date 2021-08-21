<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Resource\Resource;

interface Action
{
    public function execute(Resource $resource): void;
}
