<?php

namespace AkeneoEtl\Domain;

class EtlProfile
{
    private EtlExtractProfile $extractProfile;
    private EtlTransformProfile $transformProfile;
    private EtlLoadProfile $loadProfile;

    public function __construct(EtlExtractProfile $extractProfile, EtlTransformProfile $transformProfile, EtlLoadProfile $loadProfile)
    {
        $this->extractProfile = $extractProfile;
        $this->transformProfile = $transformProfile;
        $this->loadProfile = $loadProfile;
    }

    public function getExtractProfile(): EtlExtractProfile
    {
        return $this->extractProfile;
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
