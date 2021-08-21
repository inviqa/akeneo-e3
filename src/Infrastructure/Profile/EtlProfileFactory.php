<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Profile;

use AkeneoE3\Domain\Profile\EtlProfile;
use Symfony\Component\Yaml\Yaml;

final class EtlProfileFactory
{
    public function fromFile(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return EtlProfile::fromArray($profileData);
    }
}
