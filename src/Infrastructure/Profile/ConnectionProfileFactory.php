<?php

declare(strict_types=1);

namespace AkeneoE3\Infrastructure\Profile;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use Symfony\Component\Yaml\Yaml;

final class ConnectionProfileFactory
{
    public function fromFile(string $fileName): ConnectionProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return ConnectionProfile::fromArray($profileData);
    }
}
