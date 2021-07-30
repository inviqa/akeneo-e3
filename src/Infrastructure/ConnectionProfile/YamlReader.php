<?php

namespace App\Infrastructure\ConnectionProfile;

use App\Domain\ConnectionProfile;
use Symfony\Component\Yaml\Yaml;

class YamlReader
{
    public function read(string $fileName): ConnectionProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return ConnectionProfile::fromUserNameAndPassword(
            $profileData['host'],
            $profileData['clientId'],
            $profileData['clientSecret'],
            $profileData['userName'],
            $profileData['userPassword']
        );
    }
}
