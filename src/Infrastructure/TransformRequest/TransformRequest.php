<?php

namespace AkeneoE3\Infrastructure\TransformRequest;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\ResourceType;

class TransformRequest
{
    public ConnectionProfile $sourceConnection;

    public ConnectionProfile $destinationConnection;

    public EtlProfile $ruleProfile;

    public ResourceType $resourceType;
}
