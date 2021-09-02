<?php

declare(strict_types=1);

namespace AkeneoE3\Domain\Resource;

use Generator;

final class AuditableResource implements Resource
{
    private NonAuditableResource $resource;

    private NonAuditableResource $changes;

    private NonAuditableResource $origins;

    private function __construct(array $data, ResourceType $resourceType)
    {
        $this->resource = NonAuditableResource::fromArray($data, $resourceType);

        $this->changes = NonAuditableResource::fromCode($this->resource->getCode(), $this->resource->getResourceType());
        $this->origins = NonAuditableResource::fromCode($this->resource->getCode(), $this->resource->getResourceType());
    }

    public static function fromArray(array $data, ResourceType $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromCode(string $code, ResourceType $resourceType): self
    {
        return new self([
            $resourceType->getCodeFieldName() => $code
        ], $resourceType);
    }

    public function getResourceType(): ResourceType
    {
        return $this->resource->getResourceType();
    }

    /**
     * @return mixed
     */
    public function get(Field $field)
    {
        return $this->resource->get($field);
    }

    /**
     * @param mixed $newValue
     */
    public function set(Field $field, $newValue): void
    {
        $this->trackOrigins($field);

        $this->resource->set($field, $newValue);

        $this->trackChanges($field, $newValue);
    }

    public function addTo(Field $field, array $newValue): void
    {
        $this->trackOrigins($field);

        $this->resource->addTo($field, $newValue);

        $this->trackChanges($field, $newValue);
    }

    public function removeFrom(Field $field, array $newValue): void
    {
        $this->trackOrigins($field);

        $this->resource->removeFrom($field, $newValue);

        $this->trackChanges($field, $newValue);
    }

    public function has(Field $field): bool
    {
        return $this->resource->has($field);
    }

    public function getCode(): string
    {
        return $this->resource->getCode();
    }

    public function setCode(string $code): void
    {
        $this->resource->setCode($code);
    }

    public function isChanged(): bool
    {
        return $this->resource->isChanged();
    }

    /**
     * @return Generator|Field[]
     */
    public function fields(): Generator
    {
        return $this->resource->fields();
    }

    public function toArray(bool $full, array $ignoredFields = []): array
    {
        return ($full === false) ?
            $this->changes->toArray($full) :
            $this->resource->toArray($full);
    }

    public function __clone()
    {
        $this->resource = clone $this->resource;
    }

    public function changes(): array
    {
        return $this->changes->toArray();
    }

    public function origins(): array
    {
        return $this->origins->toArray();
    }

    private function trackOrigins(Field $field): void
    {
        if ($this->resource->has($field) === true) {
            $this->origins->set($field, $this->resource->get($field));
        }
    }

    /**
     * @param mixed $newValue
     */
    private function trackChanges(Field $field, $newValue): void
    {
        $this->changes->set($field, $this->resource->get($field));
    }
}
