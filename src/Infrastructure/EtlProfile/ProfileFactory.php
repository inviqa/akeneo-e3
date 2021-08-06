<?php

namespace AkeneoEtl\Infrastructure\EtlProfile;

use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Domain\Profile\ExtractProfile;
use AkeneoEtl\Domain\Profile\LoadProfile;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Profile\TransformProfile;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;

class ProfileFactory
{
    private ActionFactory $actionFactory;

    private ?ValidatorInterface $validator;

    public function __construct(ActionFactory $actionFactory, ValidatorInterface $validator = null)
    {
        $this->actionFactory = $actionFactory;
        $this->validator = $validator;
    }

    public function fromFile(string $fileName): EtlProfile
    {
        $profileData = Yaml::parseFile($fileName);

        return $this->fromArray($profileData);
    }

    public function fromArray(array $profileData): EtlProfile
    {
        // @todo: replace validator with optionsresolver
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

    private function createExtractProfile(array $profileData): ExtractProfile
    {
        return ExtractProfile::fromArray($profileData['extract'] ?? []);
    }

    private function createLoadProfile(array $profileData): LoadProfile
    {
        return LoadProfile::fromArray($profileData['load'] ?? []);
    }

    private function createTransformProfile(array $profileData): TransformProfile
    {
        return TransformProfile::fromArray($profileData['transform']);
    }

    private function validate(array $profileData): void
    {
        if ($this->validator === null) {
            return;
        }

        $constraint = new Assert\Collection([
            'extract' => new Assert\Optional(
                new Assert\Collection([
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
            ),
            'transform' => new Assert\Collection([
                'actions' => new Assert\Optional([
                    new Assert\Type('array'),
                    new Assert\Count(['min' => 1]),
                ]),
            ]),
            'load' => new Assert\Optional(
                new Assert\Collection([
                    'type' => new Assert\Optional([
                        new Assert\Type('string'),
                    ]),
                ]),
            ),
        ]);

        $violations = $this->validator->validate($profileData, $constraint);

        if ($violations->count() > 0) {
            throw new RuntimeException('ETL profile (transformation actions) is not valid.');
        }
    }
}
