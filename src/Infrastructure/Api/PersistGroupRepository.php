<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\PersistRepository;
use AkeneoE3\Domain\Resource\WritableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class PersistGroupRepository implements PersistRepository
{
    private PersistRepository $repository;

    private iterable $groupFieldNames;

    private string $currentGroup;

    public function __construct(PersistRepository $repository, array $groupFieldNames)
    {
        $this->repository = $repository;
        $this->groupFieldNames = $groupFieldNames;
        $this->currentGroup = '';
    }

    public function persist(WritableResource $resource, bool $patch): iterable
    {
        // If the new resource belongs to another group
        // e.g. an attribute option of attribute2
        // then flush options of attribute 1 from buffer
        if ($this->isGroupChanged($resource) === true) {
            yield from $this->flush($patch);
        }

        // change group and persist new resource
        $this->currentGroup = $this->getGroup($resource);

        yield from $this->repository->persist($resource, $patch);
    }

    public function flush(bool $patch): iterable
    {
        yield from $this->repository->flush($patch);
    }

    private function getGroup(WritableResource $resource): string
    {
        $values = [];
        foreach ($this->groupFieldNames as $groupFieldName) {
            $values[] = $resource->get(Property::create($groupFieldName));
        }

        return implode('.', $values);
    }

    private function isGroupChanged(WritableResource $resource): bool
    {
        return
            $this->currentGroup !== '' &&
            $this->currentGroup !== $this->getGroup($resource);
    }
}
