<?php

namespace AkeneoE3\Domain\Resource;

use Generator;

interface ImmutableResource
{
    public function getResourceType(): ResourceType;

    /**
     * @return mixed
     */
    public function get(Field $field);

    public function has(Field $field): bool;

    public function getCode(): string;

    /**
     * @return Generator|Field[]
     */
    public function fields(): Generator;

    public function toArray(): array;

    public function isChanged(): bool;

    public function changes(): array;

    public function origins(): array;
}
