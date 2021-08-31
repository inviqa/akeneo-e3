<?php

namespace AkeneoE3\Domain\Resource;

use AkeneoE3\Domain\Resource\ImmutableResource;

class ResourceCollection
{
    /**
     * @var ImmutableResource[]
     */
    private array $items = [];

    public function add(ImmutableResource $resource): void
    {
        $this->items[$resource->getCode()] = $resource;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function toArray(bool $full): array
    {
        return array_values(array_map(
            function (Resource $resource) use ($full) {
                return $resource->toArray($full);
            },
            $this->items
        ));
    }

    public function getResourceType(): ResourceType
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey]->getResourceType();
    }

    public function get(string $code): ImmutableResource
    {
        return $this->items[$code];
    }

    public function getFirst(): ImmutableResource
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey];
    }

    public function clear(): void
    {
        $this->items = [];
    }
}
