<?php

namespace AkeneoE3\Infrastructure\Api\ExpressionObject;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Application\Expression\ExpressionObject;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\RepositoryFactory;

class AkeneoObject implements ExpressionObject
{
    private RepositoryFactory $repositoryFactory;

    private AkeneoPimEnterpriseClientInterface $client;

    public function __construct(
        RepositoryFactory $repositoryFactory,
        AkeneoPimEnterpriseClientInterface $client
    ) {
        $this->repositoryFactory = $repositoryFactory;
        $this->client = $client;
    }

    public function getName(): string
    {
        return 'akeneo';
    }

    public function getReferenceEntityAttributeCodesByTypes(string $entityCode, array $types): array
    {
        $repository = $this->repositoryFactory->createReadRepository(
            ResourceType::referenceEntityAttribute(),
            $this->client
        );

        $query = new ApiQuery();
        $query->addFilter(ResourceType::REFERENCE_ENTITY_CODE_FIELD, '=', $entityCode);
        $attributes = $repository->read($query);

        $codes = [];

        foreach ($attributes as $attribute) {
            $type = $attribute->get(Property::create('type'));
            if (in_array($type, $types)) {
                $codes[] = $attribute->getCode();
            }
        }

        return $codes;
    }
}
