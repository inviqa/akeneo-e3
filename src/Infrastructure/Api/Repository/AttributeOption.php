<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\WriteResultFactory;

final class AttributeOption implements ReadResourcesRepository, WriteResourcesRepository, DependantResourceApi
{
    private AttributeOptionApiInterface $optionApi;

    private AttributeApiInterface $attributeApi;

    private ResourceType $resourceType;

    private array $filterForSelectTypeAttributes;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->optionApi = $client->getAttributeOptionApi();
        $this->attributeApi = $client->getAttributeApi();

        $this->filterForSelectTypeAttributes = $this->getFilterForSelectTypeAttributes();
    }

    public function count(ApiQuery $query): int
    {
        // @todo: if all options requested - count in all attributes
        return -1;
    }

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable
    {
        if ($query->hasValue(ResourceType::ATTRIBUTE_CODE_FIELD) === true) {
            $attributeCode = (string)$query->getValue(ResourceType::ATTRIBUTE_CODE_FIELD);

            return $this->readByAttributeCode($attributeCode, $query);
        }

        $attributes = $this->attributeApi->all(100, $this->filterForSelectTypeAttributes);

        foreach ($attributes as $attribute) {
            yield from $this->readByAttributeCode($attribute['code'], $query);
        }
    }

    /**
     * @return WriteResult[]
     */
    public function write(ResourceCollection $resources, bool $patch = true): iterable
    {
        if ($resources->count() === 0) {
            return [];
        }

        $attributeCode = $resources->getFirst()->get(Property::create(ResourceType::ATTRIBUTE_CODE_FIELD));

        $response = $this->optionApi->upsertList($attributeCode, $resources->toArray());

        return WriteResultFactory::createFromResponse($response, $resources);
    }

    private function readByAttributeCode(string $attributeCode, ApiQuery $query): iterable
    {
        $cursor = $this->optionApi->all($attributeCode, 100, $query->getSearchFilters([ResourceType::ATTRIBUTE_CODE_FIELD]));

        foreach ($cursor as $resource) {
            $resource[ResourceType::ATTRIBUTE_CODE_FIELD] = $attributeCode;
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }

    private function getFilterForSelectTypeAttributes(): array
    {
        $query = new ApiQuery();

        $query->addFilter(
            'type',
            'IN',
            ['pim_catalog_simpleselect', 'pim_catalog_multiselect']
        );

        return $query->getSearchFilters([]);
    }

    public function getParentFields(): array
    {
        return [ResourceType::ATTRIBUTE_CODE_FIELD];
    }
}
