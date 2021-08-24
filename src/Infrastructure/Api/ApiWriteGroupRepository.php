<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Repository\WriteRepository;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Infrastructure\Api\Repository\WriteResourcesRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class ApiWriteGroupRepository implements WriteRepository
{
    private WriteRepository $repository;

    private iterable $groupFieldNames;

    private string $currentGroup;

    public function __construct(WriteRepository $repository, array $groupFieldNames)
    {
        $this->repository = $repository;
        $this->groupFieldNames = $groupFieldNames;
        $this->currentGroup = '';
    }

    public function persist(ImmutableResource $resource, bool $patch): iterable
    {
        if ($this->isGroupChanged($resource) === true) {
            yield from $this->flush($patch);
        }

        $this->currentGroup = $this->getGroup($resource);

        return $this->repository->persist($resource, $patch);
    }

    public function flush(bool $patch): iterable
    {
        return $this->repository->flush($patch);
    }

    private function getGroup(ImmutableResource $resource): string
    {
        $values = [];
        foreach ($this->groupFieldNames as $groupFieldName) {
            $values[] = $resource->get(Property::create($groupFieldName));
        }

        return implode('.', $values);
    }

    private function isGroupChanged(ImmutableResource $resource): bool
    {
        return
            $this->currentGroup !== '' &&
            $this->currentGroup !== $this->getGroup($resource);
    }
}
