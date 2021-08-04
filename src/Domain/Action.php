<?php

namespace AkeneoEtl\Domain;

interface Action
{
    public function getType(): string;

    public function execute(Resource $resource): ?array;
}
