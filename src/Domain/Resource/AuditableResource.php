<?php

namespace AkeneoE3\Domain\Resource;

interface AuditableResource
{
    public function isChanged(): bool;

    public function changes(): BaseResource;

    public function origins(): BaseResource;
}
