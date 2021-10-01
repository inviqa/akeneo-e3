<?php

namespace AkeneoE3\Infrastructure\Request;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Profile\ConnectionProfileFactory;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

class TransformRequestFactory
{
    private ConnectionProfileFactory $connectionProfileFactory;

    public function __construct(
        ConnectionProfileFactory $connectionProfileFactory
    ) {
        $this->connectionProfileFactory = $connectionProfileFactory;
    }

    public function createFromInput(InputInterface $input): TransformRequest
    {
        $request = new TransformRequest();

        $request->sourceConnection = $this->getConnectionProfile($input);
        $request->destinationConnection = $this->getDestinationConnectionProfile($input) ?? $request->sourceConnection;

        $request->ruleProfile = $this->getEtlProfile($input);

        return $request;
    }

    private function getConnectionProfile(InputInterface $input): ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('connection');

        if ($profileFileName === '') {
            throw new LogicException(
                '--connection option is required.'
            );
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getDestinationConnectionProfile(InputInterface $input): ?ConnectionProfile
    {
        $profileFileName = (string)$input->getOption('destination-connection');

        if ($profileFileName === '') {
            return null;
        }

        return $this->connectionProfileFactory->fromFile($profileFileName);
    }

    private function getEtlProfile(InputInterface $input): EtlProfile
    {
        $profileFileName = (string)$input->getOption('rules');

        if ($profileFileName === '') {
            throw new LogicException('rules option is required.');
        }

        $resourceType = ResourceType::create((string)$input->getOption('resource-type'));
        $ruleProfileData = Yaml::parseFile($profileFileName);

        $profile = EtlProfile::fromConfiguration(
            $resourceType,
            $ruleProfileData
        );

        $profile->setDryRun($this->isDryRun($input));
        $profile->setDryRunCodes($this->getDryRunCodes($input));

        return $profile;
    }

    private function isDryRun(InputInterface $input): bool
    {
        return $input->getOption('dry-run') !== '';
    }

    private function getDryRunCodes(InputInterface $input): array
    {
        $dryRunOption = (string)$input->getOption('dry-run');

        if ($dryRunOption === '') {
            return [];
        }

        return explode(',', $dryRunOption);
    }
}
