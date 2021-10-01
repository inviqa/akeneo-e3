<?php

namespace AkeneoE3\Infrastructure\Request;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\ResourceType;

class RuleSetTransformRequest
{
    public ConnectionProfile $sourceConnection;

    public ConnectionProfile $destinationConnection;

    /**
     * @var EtlProfile[]
     */
    public array $ruleSet;

    public bool $isDryRun;
}
