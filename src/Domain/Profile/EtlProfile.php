<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

final class EtlProfile
{
    private ExtractProfile $extractProfile;
    private TransformProfile $transformProfile;
    private LoadProfile $loadProfile;

    public function __construct(ExtractProfile $extractProfile, TransformProfile $transformProfile, LoadProfile $loadProfile)
    {
        $this->extractProfile = $extractProfile;
        $this->transformProfile = $transformProfile;
        $this->loadProfile = $loadProfile;
    }

    public function getExtractProfile(): ExtractProfile
    {
        return $this->extractProfile;
    }

    public function getLoadProfile(): LoadProfile
    {
        return $this->loadProfile;
    }

    public function getTransformProfile(): TransformProfile
    {
        return $this->transformProfile;
    }
}
