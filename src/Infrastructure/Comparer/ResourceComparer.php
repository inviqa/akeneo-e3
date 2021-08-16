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

    /**
     * @return array|DiffLine[]
     */
    public function compareWithOrigin(Resource $resource): array
    {
        if ($resource->getOrigin() === null) {
            return $this->compareResources(null, $resource);
        }

        return $this->compareResources(
            $resource->getOrigin()->diff($resource),
            $resource->diff($resource->getOrigin())
        );
    }

    /**
     * @return array|DiffLine[]
     */
    protected function compareResources(?Resource $resource1, Resource $resource2): array
    {
        $comparison = [];

        foreach ($resource2->fields() as $field) {
            if ($field->getName() === $resource2->getCodeFieldName()) {
                continue;
            }

            $originalValue = $resource1 !== null && $resource1->has($field) ?
                $this->normaliser->normalise($resource1->get($field)) : '';

            $newValue = $this->normaliser->normalise($resource2->get($field));

            $comparison[] = DiffLine::create(
                $resource2->getCodeOrIdentifier() ?? '',
                $field,
                $originalValue,
                $newValue
            );
        }

        return $comparison;
    }
}
