<?php

declare(strict_types=1);

namespace AkeneoEtl\Domain\Profile;

use Symfony\Component\OptionsResolver\OptionsResolver;

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

    public static function fromArray(array $data): self
    {
        $data = self::resolve($data);

        return new self(
            ExtractProfile::fromArray($data['extract'] ?? []),
            TransformProfile::fromArray($data['transform'] ?? []),
            LoadProfile::fromArray($data['load'] ?? []),
        );
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

    private static function resolve(array $data): array
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('transform')
            ->setDefined(['extract', 'load'])
            ->setAllowedTypes('extract', 'array')
            ->setAllowedTypes('transform', 'array')
            ->setAllowedTypes('load', 'array');

        return $data;
    }
}
