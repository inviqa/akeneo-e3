<?php

namespace AkeneoEtl\Infrastructure\ConnectionProfile;

use AkeneoEtl\Domain\Profile\ConnectionProfile;
use Symfony\Component\Yaml\Yaml;

class ProfileFactory
{
    public function read(string $fileName): ConnectionProfile
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
