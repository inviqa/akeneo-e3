<?php

namespace AkeneoE3\Domain\Resource;

use AkeneoE3\Domain\Resource\Resource;

class ResourceCollection
{
    /**
     * @var Resource[]
     */
    private array $items = [];

    public function add(Resource $resource): void
    {
        $this->items[$resource->getCode()] = $resource;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function toArray(bool $full, array $ignoredFields = []): array
    {
        return array_map(
            function (Resource $resource) use ($full, $ignoredFields) {
                return $resource->toArray($full, $ignoredFields);
            },
            $this->items
        );
    }

    public function getResourceType(): ResourceType
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey]->getResourceType();
    }

    public function get(string $code): Resource
    {
        return $this->items[$code];
    }

    public function getFirst(): Resource
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey];
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
