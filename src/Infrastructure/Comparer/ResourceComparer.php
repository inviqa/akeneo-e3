<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Comparer;

use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\ImmutableResource;
use AkeneoE3\Infrastructure\Command\ResourceDataNormaliser;

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
    public function compareWithOrigin(ImmutableResource $resource): array
    {
        if (!$resource instanceof AuditableResource) {
            return [];
        }

        $comparison = [];

        foreach ($resource->changes() as $field => $fieldValue) {

            // skip code
            if ($field === $resource->getResourceType()->getCodeFieldName()) {
                continue;
            }

            $originalValue = $resource->origins()[$field] ?? '';
            $newValue = $fieldValue;

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
