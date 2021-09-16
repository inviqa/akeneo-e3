<?php

namespace AkeneoE3\Infrastructure\Api\ExpressionObject;

use Akeneo\PimEnterprise\ApiClient\AkeneoPimEnterpriseClientInterface;
use AkeneoE3\Application\Expression\ExpressionObject;
use AkeneoE3\Domain\Resource\Property;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Api\Query\ApiQuery;
use AkeneoE3\Infrastructure\Api\RepositoryFactory;
use LogicException;
use Traversable;

class AkeneoObject implements ExpressionObject
{
    private RepositoryFactory $repositoryFactory;

    private AkeneoPimEnterpriseClientInterface $client;

    private array $cache = [];

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
        $attributes = $this->fetchAttributes($entityCode);

        $codes = [];

        foreach ($attributes as $attribute) {
            $type = $attribute->get(Property::create('type'));
            if (in_array($type, $types)) {
                $codes[] = $attribute->getCode();
            }
        }

        return $codes;
    }

    private function fetchAttributes(string $entityCode): array
    {
        if (isset($this->cache[__FUNCTION__][$entityCode]) === true) {
            return $this->cache[__FUNCTION__][$entityCode];
        }

        $repository = $this->repositoryFactory->createReadRepository(
            ResourceType::referenceEntityAttribute(),
            $this->client
        );

        $query = new ApiQuery();
        $query->addFilter(
            ResourceType::REFERENCE_ENTITY_CODE_FIELD,
            '=',
            $entityCode
        );

        $attributes = $this->convertIterableToArray($repository->read($query));

        $this->cache[__FUNCTION__][$entityCode] = $attributes;

        return $attributes;
    }

    private function convertIterableToArray(iterable $data): array
    {
        if (is_array($data) === true) {
            return $data;
        }

        if ($data instanceof Traversable) {
            return iterator_to_array($data);
        }
    }
}
