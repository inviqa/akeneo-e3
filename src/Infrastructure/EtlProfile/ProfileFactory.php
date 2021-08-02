<?php

namespace AkeneoEtl\Infrastructure\EtlProfile;

use AkeneoEtl\Application\TransformerStepFactory;
use AkeneoEtl\Domain\EtlLoadProfile;
use AkeneoEtl\Domain\EtlProfile;
use AkeneoEtl\Domain\EtlTransformProfile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class ProfileFactory
{
    private TransformerStepFactory $stepFactory;

    private ValidatorInterface $validator;

    public function __construct(TransformerStepFactory $stepFactory, ValidatorInterface $validator)
    {
        $this->stepFactory = $stepFactory;
        $this->validator = $validator;
    }

    public function fromFile(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);
        $this->validate($profileData);

        $transformProfile = $this->createTransformProfile($profileData);
        $loadProfile = $this->createLoadProfile($profileData);

        return new EtlProfile(
            $profileData['extract']['query'] ?? [],
            $transformProfile,
            $loadProfile
        );
    }

    private function createLoadProfile(array $profileData): EtlLoadProfile
    {
        return EtlLoadProfile::fromArray($profileData['load'] ?? []);
    }

    private function createTransformProfile(array $profileData): EtlTransformProfile
    {
        $steps = [];
        foreach ($profileData['transform']['steps'] ?? [] as $stepData) {
            // @todo: throw exception if no type

            $stepType = $stepData['type'];
            $steps[] = $this->stepFactory->create($stepType, $stepData);
        }

        return new EtlTransformProfile($steps);
    }

    private function validate(array $profileData): void
    {
        $constraint = new Assert\Collection([
            'extract' => new Assert\Collection([
                'query' => new Assert\Collection([
                    'filters' => new Assert\Optional([
                        new Assert\Type('array'),
                        new Assert\Count(['min' => 1]),
                        new Assert\All([
                            new Assert\Collection([
                                'property' => new Assert\Type('string'),
                                'operator' => new Assert\Type('string'),
                                'value' => new Assert\Optional(),
                                'options' => new Assert\Optional(),
                            ]),
                        ]),
                    ]),
                ]),
            ]),
            'transform' => new Assert\Collection([
                'steps' => new Assert\Optional([
                    new Assert\Type('array'),
                    new Assert\Count(['min' => 1]),
                ]),
            ]),
            'load' => new Assert\Collection([
                'type' => new Assert\Optional([
                    new Assert\Type('string'),
                ]),
            ]),
        ]);

        $violations = $this->validator->validate($profileData, $constraint);

        // check violations and throw exception
        // for details create a separate command profile:etl:validate
        // (as profile:etl:create)
    }
}
