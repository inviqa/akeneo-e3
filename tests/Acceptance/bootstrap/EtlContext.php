<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Application\ActionFactory;
use AkeneoEtl\Domain\EtlProcess;
use AkeneoEtl\Domain\Hook\EmptyHooks;
use AkeneoEtl\Domain\Resource;
use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Infrastructure\EtlFactory;
use AkeneoEtl\Infrastructure\EtlProfile\ProfileFactory;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class EtlContext implements Context
{
    private \AkeneoEtl\Domain\Resource $resource;

    private InMemoryExtractor $extractor;

    private Transformer $transformer;

    private InMemoryLoader $loader;

    public function __construct()
    {
    }

    /**
     * @Given a :resourceType in the PIM:
     */
    public function createResource(string $resourceType, TableNode $table)
    {
        $data = $this->readResourceDataFromTable($table);

        $this->resource = Resource::fromArray($data, $resourceType);
        $this->extractor = new InMemoryExtractor($this->resource);
    }

    /**
     * @Given an ETL profile:
     */
    public function initialiseEtlProfile(PyStringNode $string)
    {
        $config = Yaml::parse($string);

        $factory = new EtlFactory();
        $profileFactory = new ProfileFactory(new ActionFactory(), );
        $profile = $profileFactory->fromArray($config);

        $transformProfile = $profile->getTransformProfile();
        $this->transformer = $factory->createTransformer($transformProfile, new EmptyHooks());
    }

    /**
     * @When transformation is executed
     */
    public function transformationIsExecuted()
    {
        $this->loader = new InMemoryLoader($this->resource);

        $etl = new EtlProcess(
            $this->extractor,
            $this->transformer,
            $this->loader,
            new EmptyHooks()
        );

        $etl->execute();
    }

    /**
     * @Then the :product in the PIM should look like:
     */
    public function checkResultInLoader(string $resourceType, TableNode $table)
    {
        $data = $this->readResourceDataFromTable($table);

        Assert::eq($data, $this->loader->getResult());
    }

    private function readResourceDataFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $data[$row['field']] = $row['value'];
        }

        return $data;
    }
}
