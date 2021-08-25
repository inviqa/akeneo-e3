<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Api\Repository;

use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Domain\Result\Write\WriteResult;
use AkeneoE3\Domain\Resource\AuditableResource;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\Resource;
use AkeneoE3\Domain\Resource\ResourceCollection;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\WriteResultFactory;

final class FamilyVariant implements ReadResourcesRepository, WriteResourcesRepository
{
    private FamilyVariantApiInterface $api;

    private ResourceType $resourceType;

    /**
     * @var \Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface
     */
    private AkeneoPimEnterpriseClientInterface $client;

    public function __construct(ResourceType $resourceType, AkeneoPimEnterpriseClientInterface $client)
    {
        $this->resourceType = $resourceType;
        $this->api = $client->getFamilyVariantApi();
        $this->client = $client;
    }

    public function count(ApiQuery $query): int
    {
        return -1;
//        (int)$this->api
//            ->listPerPage(1, true, $query->getSearchFilters())
//            ->getCount();
    }

    /**
     * @return iterable<Resource>
     */
    public function read(ApiQuery $query): iterable
    {
        if ($query->hasValue(ResourceType::FAMILY_CODE_FIELD) === true) {
            $familyCode = (string)$query->getValue(ResourceType::FAMILY_CODE_FIELD);

            return $this->readByFamilyCode($familyCode, $query);
        }

        $familyApi = $this->client->getFamilyApi();

        foreach ($familyApi->all(100) as $family) {
            yield from $this->readByFamilyCode($family['code'], $query);
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

        $familyCode = $resources->getFirst()->get(Property::create(ResourceType::FAMILY_CODE_FIELD));

        $response = $this->api->upsertList($familyCode, $resources->toArray(!$patch, [ResourceType::FAMILY_CODE_FIELD]));

        yield from WriteResultFactory::createFromResponse($response, $resources);
    }

    private function readByFamilyCode(string $familyCode, ApiQuery $query): iterable
    {
        $cursor = $this->api->all($familyCode, 100, $query->getSearchFilters([ResourceType::FAMILY_CODE_FIELD]));

        foreach ($cursor as $resource) {
            $resource[ResourceType::FAMILY_CODE_FIELD] = $familyCode;
            yield AuditableResource::fromArray($resource, $this->resourceType);
        }
    }
}
