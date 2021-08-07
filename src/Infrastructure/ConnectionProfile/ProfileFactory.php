<?php

declare(strict_types=1);

namespace AkeneoEtl\Infrastructure\ConnectionProfile;

use AkeneoEtl\Domain\Profile\ConnectionProfile;
use Symfony\Component\Yaml\Yaml;

final class ProfileFactory
{
    public function fromFile(string $fileName): ConnectionProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return ConnectionProfile::fromUser(
            $profileData['host'],
            $profileData['clientId'],
            $profileData['clientSecret'],
            $profileData['userName'],
            $profileData['userPassword']
        );
    }
}
