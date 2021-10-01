<?php

namespace AkeneoE3\Infrastructure\Request;

use AkeneoE3\Domain\Profile\ConnectionProfile;
use AkeneoE3\Domain\Profile\EtlProfile;
use AkeneoE3\Domain\Resource\ResourceType;
use AkeneoE3\Infrastructure\Profile\ConnectionProfileFactory;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

class RuleSetTransformRequestFactory
{
    private ConnectionProfileFactory $connectionProfileFactory;

    public function __construct(
        ConnectionProfileFactory $connectionProfileFactory
    ) {
        $this->connectionProfileFactory = $connectionProfileFactory;
    }

    public function createFromInput(InputInterface $input): RuleSetTransformRequest
    {
        $request = new RuleSetTransformRequest();

        $request->sourceConnection = $this->getConnectionProfile($input);
        $request->destinationConnection = $this->getDestinationConnectionProfile($input) ?? $request->sourceConnection;
        $request->ruleSet = $this->getRuleSetProfile($input);
        $request->isDryRun = $this->isDryRun($input);

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

    /**
     * @return EtlProfile[]
     */
    private function getRuleSetProfile(InputInterface $input): array
    {
        $profileFileName = (string)$input->getOption('rules');

        if ($profileFileName === '') {
            throw new LogicException('rules option is required.');
        }

        $dryRun = $this->isDryRun($input);

        $ruleSetData = Yaml::parseFile($profileFileName);

        $ruleSet = [];
        foreach ($ruleSetData as $ruleData) {
            $resourceType = $ruleData['resource'];
            unset($ruleData['resource']);

            $ruleProfile = EtlProfile::fromConfiguration(
                ResourceType::create($resourceType),
                $ruleData
            );

            $ruleProfile->setDryRun($dryRun);

            $ruleSet[] = $ruleProfile;
        }

        return $ruleSet;
    }

    private function isDryRun(InputInterface $input): bool
    {
        return $input->getOption('dry-run') !== '';
    }
}
