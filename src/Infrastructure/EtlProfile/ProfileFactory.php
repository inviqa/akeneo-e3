<?php

namespace AkeneoEtl\Infrastructure\EtlProfile;

use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Domain\EtlExtractProfile;
use AkeneoEtl\Domain\EtlLoadProfile;
use AkeneoEtl\Domain\EtlProfile;
use AkeneoEtl\Domain\EtlTransformProfile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class ProfileFactory
{
    private ActionFactory $actionFactory;

    private ValidatorInterface $validator;

    public function __construct(ActionFactory $actionFactory, ValidatorInterface $validator)
    {
        $this->actionFactory = $actionFactory;
        $this->validator = $validator;
    }

    public function fromFile(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);
        $this->validate($profileData);

        $extractProfile = $this->createExtractProfile($profileData);
        $transformProfile = $this->createTransformProfile($profileData);
        $loadProfile = $this->createLoadProfile($profileData);

        return new EtlProfile(
            $extractProfile,
            $transformProfile,
            $loadProfile
        );
    }

    private function createExtractProfile(array $profileData): EtlExtractProfile
    {
        return EtlExtractProfile::fromArray($profileData['extract'] ?? []);
    }

    private function createLoadProfile(array $profileData): EtlLoadProfile
    {
        return EtlLoadProfile::fromArray($profileData['load'] ?? []);
    }

    private function createTransformProfile(array $profileData): EtlTransformProfile
    {
        $actions = [];
        foreach ($profileData['transform']['actions'] ?? [] as $actionData) {
            // @todo: throw exception if no type

            $type = $actionData['type'];
            $actions[] = $this->actionFactory->create($type, $actionData);
        }

        return EtlTransformProfile::fromActions($actions);
    }

    private function validate(array $profileData): void
    {
        $constraint = new Assert\Collection([
            'extract' => new Assert\Collection([
                'conditions' => new Assert\Optional([
                    new Assert\Type('array'),
                    new Assert\Count(['min' => 1]),
                    new Assert\All([
                        new Assert\Collection([
                            'field' => new Assert\Type('string'),
                            'operator' => new Assert\Type('string'),
                            'value' => new Assert\Optional(),
                        ]),
                    ]),
                ]),
            ]),
            'transform' => new Assert\Collection([
                'actions' => new Assert\Optional([
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
