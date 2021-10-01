<?php

namespace AkeneoE3\Domain\Resource;

class ResourceCollection
{
    /**
     * @var WritableResource[]
     */
    private array $items = [];

    public function add(WritableResource $resource): void
    {
        $this->items[$resource->getCode()] = $resource;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function changes(): array
    {
        return array_values(array_map(
            function (WritableResource $resource) {
                return $resource->changes()->toArray();
            },
            $this->items
        ));
    }

    public function getResourceType(): ResourceType
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey]->getResourceType();
    }

    public function get(string $code): WritableResource
    {
        return $this->items[$code];
    }

    public function getFirst(): WritableResource
    {
        $firstKey = array_key_first($this->items);

        return $this->items[$firstKey];
    }

    public function clear(): void
    {
        $this->items = [];
    }

    /**
     * @return WritableResource[]
     */
    public function items(): array
    {
        return $this->items;
    }
}
