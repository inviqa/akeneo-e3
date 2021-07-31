<?php

namespace AkeneoEtl\Infrastructure\EtlProfile;

use AkeneoEtl\Application\TransformerStepFactory;
use AkeneoEtl\Domain\EtlLoadProfile;
use AkeneoEtl\Domain\EtlProfile;
use AkeneoEtl\Domain\EtlTransformProfile;
use Symfony\Component\Yaml\Yaml;

class YamlReader
{
    private TransformerStepFactory $stepFactory;

    public function __construct(TransformerStepFactory $stepFactory)
    {
        $this->stepFactory = $stepFactory;
    }

    public function read(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);

        $steps = [];
        foreach ($profileData['transform']['steps'] ?? [] as $stepData) {
            // @todo: throw exception if no type

            $stepType = $stepData['type'];
            $steps[] = $this->stepFactory->create($stepType, $stepData);
        }

        $transformProfile = new EtlTransformProfile($steps);

        $loaderProfile = new EtlLoadProfile();
        $loaderProfile->isDryRun = ($profileData['load']['mode'] ?? '') === 'dry-run';

        return new EtlProfile(
            $profileData['extract']['query'] ?? [],
            $transformProfile,
            $loaderProfile
        );
    }
}
