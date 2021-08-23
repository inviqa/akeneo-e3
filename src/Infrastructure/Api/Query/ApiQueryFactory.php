<?php

namespace AkeneoE3\Infrastructure\Api\Query;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Repository\Query;
use AkeneoE3\Domain\Repository\QueryFactory;
use AkeneoE3\Domain\Resource\ResourceType;

class ApiQueryFactory implements QueryFactory
{
    public function fromProfile(ExtractProfile $profile, ResourceType $resourceType): Query
    {
        return ApiQuery::fromProfile($profile, $resourceType);
    }
}
