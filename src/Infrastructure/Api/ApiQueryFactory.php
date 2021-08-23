<?php

namespace AkeneoE3\Infrastructure\Api;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\QueryFactory;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Extractor\ApiQuery;

class ApiQueryFactory implements QueryFactory
{
    public function fromProfile(ExtractProfile $profile, ResourceType $resourceType): Query
    {
        return ApiQuery::fromProfile($profile, $resourceType);
    }
}
