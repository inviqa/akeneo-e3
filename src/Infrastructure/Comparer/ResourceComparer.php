<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\Comparer;

use AkeneoEtl\Domain\Resource\AuditableResource;
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
        if (!$resource instanceof AuditableResource) {
            return [];
        }

        $comparison = [];

        foreach ($resource->changes()->fields() as $field) {

            // skip code
            if ($field->getName() === $resource->getCodeFieldName()) {
                continue;
            }

            $originalValue = $resource->origins()->has($field) ? $resource->origins()->get($field) : '';
            $newValue = $resource->changes()->get($field);

            $comparison[] = DiffLine::create(
                $resource->getCode() ?? '',
                $field,
                $this->normaliser->normalise($originalValue),
                $this->normaliser->normalise($newValue)
            );
        }

        return $comparison;
    }
}
