<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\EtlProfile;

use AkeneoEtl\Domain\Profile\EtlProfile;
use Symfony\Component\Yaml\Yaml;

final class ProfileFactory
{
    public function fromFile(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return EtlProfile::fromArray($profileData);
    }
}
