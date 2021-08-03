<?php

namespace AkeneoEtl\Domain;

interface Loader
{
    public function queue(array $item): void;

    public function load(): void;
}
