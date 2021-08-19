<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Resource;

use Generator;

final class AuditableResource implements Resource
{
    private Resource $resource;

    private Resource $changes;

    private Resource $origins;

    private function __construct(array $data, string $resourceType)
    {
        $this->resource = NonAuditableResource::fromArray($data, $resourceType);

        $this->changes = NonAuditableResource::fromCode($this->resource->getCode(), $this->resource->getResourceType());
        $this->origins = NonAuditableResource::fromCode($this->resource->getCode(), $this->resource->getResourceType());
    }

    public static function fromArray(array $data, string $resourceType): self
    {
        return new self($data, $resourceType);
    }

    public static function fromCode(string $code, string $resourceType): self
    {
        return new self([
            'identifier' => $code
        ], $resourceType);
    }

    public function getResourceType(): string
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
    public function set(Field $field, $newValue): Resource
    {
        $this->track($field, $newValue);

        $this->resource->set($field, $newValue);

        return $this;
    }

    public function addTo(Field $field, array $newValue): Resource
    {
        $this->track($field, $newValue);

        $this->resource->addTo($field, $newValue);

        return $this;
    }

    public function has(Field $field): bool
    {
        return $this->resource->has($field);
    }

    public function getCode(): string
    {
        return $this->resource->getCode();
    }

    public function setCode(string $code): Resource
    {
        $this->resource->setCode($code);

        return $this;
    }

    public function getCodeFieldName(): string
    {
        return $this->resource->getCodeFieldName();
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

    public function toArray(): array
    {
        return $this->resource->toArray();
    }

    public function __clone()
    {
        $this->resource = clone $this->resource;
    }

    public function changes(): Resource
    {
        return $this->changes;
    }

    public function origins(): Resource
    {
        return $this->origins;
    }

    /**
     * @param mixed $newValue
     */
    private function track(Field $field, $newValue): void
    {
        if ($this->resource->has($field) === true) {
            $this->origins->set($field, $this->resource->get($field));
        }

        $this->changes->set($field, $newValue);
    }
}
