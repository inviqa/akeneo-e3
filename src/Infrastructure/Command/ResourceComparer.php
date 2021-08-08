<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Command;

final class ResourceComparer
{
    private ResourceDataNormaliser $normaliser;

    public function __construct()
    {
        $this->normaliser = new ResourceDataNormaliser();
    }

    public function getCompareTable(
        \AkeneoEtl\Domain\Resource $resource1,
        \AkeneoEtl\Domain\Resource $resource2
    ): array {
        $comparison = [];

        foreach ($resource2->fields() as $field) {
            $originalValue = $resource1->has($field) ?
                $this->normaliser->normalise($resource1->get($field)) :
                '';
            $originalValue = substr($originalValue, 0, 40);

            $newValue = $this->normaliser->normalise($resource2->get($field));
            $newValue = substr($newValue, 0, 40);

            $comparison[] = [
                $field->getName(),
                $originalValue,
                $newValue,
            ];
        }

        return $comparison;
    }
}
