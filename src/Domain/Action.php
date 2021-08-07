<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain;

interface Action
{
    public function getType(): string;

    public function execute(Resource $resource): void;
}
