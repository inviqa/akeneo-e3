<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Comparer;

use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\WritableResource;
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
    public function compareWithOrigin(WritableResource $resource): array
    {
        $comparison = [];

        foreach ($resource->changes()->fields() as $field) {

            // skip code
            if ($field->getName() === $resource->getResourceType()->getCodeFieldName()) {
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
