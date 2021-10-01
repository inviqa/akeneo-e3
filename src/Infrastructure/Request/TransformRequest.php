<?php

namespace AkeneoE3\Infrastructure\Request;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;

class TransformRequest
{
    public ConnectionProfile $sourceConnection;

    public ConnectionProfile $destinationConnection;

    public EtlProfile $ruleProfile;
}
