<?php

namespace AkeneoE3\Infrastructure\Api\Repository;

interface DependantResourceApi
{
    public function getParentFields(): array;
}
