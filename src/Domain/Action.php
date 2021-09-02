<?php

declare(strict_types=1);

namespace AkeneoE3\Domain;

use AkeneoE3\Domain\Resource\TransformableResource;

interface Action
{
    public function execute(TransformableResource $resource): void;
}
