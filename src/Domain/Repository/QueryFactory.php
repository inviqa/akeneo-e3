<?php

namespace AkeneoE3\Domain\Repository;

use AkeneoE3\Domain\Profile\ExtractProfile;
use AkeneoE3\Domain\Resource\ResourceType;

interface QueryFactory
{
    public function fromProfile(ExtractProfile $profile, ResourceType $resourceType): Query;
}
