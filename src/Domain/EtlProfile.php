<?php

namespace AkeneoEtl\Domain;

class EtlProfile
{
    private array $extractorQuery;

    private EtlLoadProfile $loadProfile;

    private EtlTransformProfile $transformProfile;

    public function __construct(array $extractorQuery, EtlTransformProfile $transformProfile, EtlLoadProfile $loadProfile)
    {
        $this->extractorQuery = $extractorQuery;
        $this->transformProfile = $transformProfile;
        $this->loadProfile = $loadProfile;
    }

    public function getExtractorQuery(): array
    {
        return $this->extractorQuery;
    }

    public function getLoadProfile(): EtlLoadProfile
    {
        return $this->loadProfile;
    }

    public function getTransformProfile(): EtlTransformProfile
    {
        return $this->transformProfile;
    }
}
