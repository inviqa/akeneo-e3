<?php

namespace AkeneoEtl\Domain;

interface Loader
{
    public function queue(Resource $resource): void;

    public function load(): void;
}
