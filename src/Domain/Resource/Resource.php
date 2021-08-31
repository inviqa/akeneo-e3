<?php

namespace AkeneoE3\Domain\Resource;

use Generator;

interface Resource extends ImmutableResource
{
    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): void;

    public function addTo(Field $field, array $newValue): void;

    public function removeFrom(Field $field, array $newValue): void;

    public function setCode(string $code): void;

    public function isChanged(): bool;
}
