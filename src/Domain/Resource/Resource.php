<?php

namespace AkeneoE3\Domain\Resource;

use Generator;

interface Resource
{
    public function getResourceType(): ResourceType;

    /**
     * @return mixed
     */
    public function get(Field $field);

    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): void;

    public function addTo(Field $field, array $newValue): void;

    public function removeFrom(Field $field, array $newValue): void;

    public function has(Field $field): bool;

    public function getCode(): string;

    public function setCode(string $code): void;

    public function getCodeFieldName(): string;

    public function isChanged(): bool;

    /**
     * @return Generator|Field[]
     */
    public function fields(): Generator;

    public function toArray(bool $full, array $ignoredFields = []): array;
}
