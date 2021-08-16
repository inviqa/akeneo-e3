<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Comparer;

use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Infrastructure\Command\ResourceDataNormaliser;

final class ResourceComparer
{
    private ResourceDataNormaliser $normaliser;

    public function __construct()
    {
        $this->normaliser = new ResourceDataNormaliser();
    }

    public function getCompareTable(?Resource $resource1, Resource $resource2): array
    {
        if ($resource1 === null) {
            return $this->getAfterTable($resource2);
        }

        return $this->getBeforeAfterTable($resource1, $resource2);
    }

    private function getBeforeAfterTable(
        Resource $resource1,
        Resource $resource2
    ): array {
        $comparison = [];

        foreach ($resource2->fields() as $field) {
            if ($field->getName() === $resource2->getCodeOrIdentifierFieldName(
                )) {
                continue;
            }

            $originalValue = $resource1->has($field) ?
                $this->normaliser->normalise($resource1->get($field)) :
                '';

            $newValue = $this->normaliser->normalise($resource2->get($field));

            $comparison[$field->getName()] = [
                $resource2->getCodeOrIdentifier(),
                $field->getName(),
                $originalValue,
                $newValue,
            ];
        }

        return $comparison;
    }

    private function getAfterTable(
        Resource $resource
    ): array {
        $comparison = [];

        foreach ($resource->fields() as $field) {
            if ($field->getName() === $resource->getCodeOrIdentifierFieldName(
                )) {
                continue;
            }

            $originalValue = $resource->has($field) ?
                $this->normaliser->normalise($resource->get($field)) :
                '';

            $comparison[$field->getName()] = [
                $resource->getCodeOrIdentifier(),
                $field->getName(),
                $originalValue,
            ];
        }

        return $comparison;
    }
}
