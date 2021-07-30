<?php

namespace App\Infrastructure\EtlProfile;

use App\Application\TransformerStepFactory;
use App\Domain\EtlLoadProfile;
use App\Domain\EtlProfile;
use App\Domain\EtlTransformProfile;
use Symfony\Component\Yaml\Yaml;

class YamlReader
{
    private TransformerStepFactory $transformerFactory;

    public function __construct(TransformerStepFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    public function read(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);

        $transformers = [];
        foreach ($profileData['transform']['transformations'] ?? [] as $transformationData) {

            $transformerName = $transformationData['name'];
            $transformers[] = $this->transformerFactory->create($transformerName, $transformationData);
        }

        $transformProfile = new EtlTransformProfile($transformers);

        $loaderProfile = new EtlLoadProfile();
        $loaderProfile->isDryRun = ($profileData['load']['mode'] ?? '') === 'dry-run';

        return new EtlProfile(
            $profileData['extract']['query'] ?? [],
            $transformProfile,
            $loaderProfile
        );
    }
}
