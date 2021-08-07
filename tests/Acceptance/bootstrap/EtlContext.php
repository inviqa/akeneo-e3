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
    private InMemoryExtractor $extractor;

    private Transformer $transformer;

    private InMemoryLoader $loader;

    private array $resourceData = [];

    private string $resourceType;

    /**
     * @Given /^(a|an) (?P<resourceType>[^"]+) in the PIM( with properties)?:$/
     */
    public function readResourceProperties(string $resourceType, TableNode $table)
    {
        $this->resourceType = $resourceType;
        $this->resourceData = $this->readPropertiesFromTable($table);
    }

    /**
     * @Given /^attributes:$/
     */
    public function readResourceValues(TableNode $table)
    {
        $this->resourceData['values'] = $this->readValuesFromTable($table);
    }

    /**
     * @Given the list of :listCode:
     */
    public function readResourceLocalisedList(string $listCode, TableNode $table)
    {
        $this->resourceData[$listCode] = $this->readLocalisedListFromTable($table);
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
        $resource = Resource::fromArray($this->resourceData, $this->resourceType);
        $this->extractor = new InMemoryExtractor($resource);

        $this->loader = new InMemoryLoader($resource);

        $etl = new EtlProcess(
            $this->extractor,
            $this->transformer,
            $this->loader,
            new EmptyHooks()
        );

        $etl->execute();
    }

    /**
     * @Then the :resourceType in the PIM should have properties:
     */
    public function checkProperties(string $resourceType, TableNode $table)
    {
        $expected = $this->readPropertiesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        unset($loaderData['values'], $loaderData['labels'], $loaderData['associations']);

        Assert::eq($expected, $loaderData);
    }

    /**
     * @Then should have attributes:
     */
    public function checkValues(TableNode $table)
    {
        $expected = $this->readValuesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        Assert::eq($expected, $loaderData['values']);
    }

    /**
     * @Then should have the list of :listCode:
     */
    public function checkLocalisedList(string $listCode, TableNode $table)
    {
        $expected = $this->readLocalisedListFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();

        Assert::eq($expected, $loaderData[$listCode]);
    }

    private function readPropertiesFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $field = $row['field'];
            $value = $this->convertTableValue($row['value']);

            $data[$field] = $value;
        }

        return $data;
    }

    private function readValuesFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $field = $row['attribute'];
            $value = $this->convertTableValue($row['value']);

            $data[$field][] = [
                'scope' => $row['scope'],
                'locale' => $row['locale'],
                'data' => $value,
            ];
        }

        return $data;
    }


    private function readLocalisedListFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $locale = $row['locale'];
            $value = $this->convertTableValue($row['value']);

            $data[$locale] = $value;
        }

        return $data;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function convertTableValue($value)
    {
        $matches = [];
        if (preg_match('/^\[(.*)\]$/', $value, $matches) !== 1) {
            return $value;
        }

        return explode(',', $matches[1]);
        return [$matches, $value];
    }
}
