<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\EtlProcess;
use AkeneoEtl\Domain\Profile\EtlProfile;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\Resource;
use AkeneoEtl\Domain\Transformer;
use AkeneoEtl\Infrastructure\EtlFactory;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class EtlContext implements Context
{
    private InMemoryExtractor $extractor;

    private Transformer $transformer;

    private InMemoryLoader $loader;

    private array $resourceData = [];

    private string $resourceType;

    private EventDispatcher $eventDispatcher;

    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    /**
     * @Given /^(a|an) (?P<resourceType>[^"]+) in the PIM( with properties)?:$/
     */
    public function readResourceProperties(string $resourceType, TableNode $table): void
    {
        $this->resourceType = $resourceType;
        $this->resourceData = $this->readPropertiesFromTable($table);
    }

    /**
     * @Given /^attributes:$/
     */
    public function readResourceValues(TableNode $table): void
    {
        $this->resourceData['values'] = $this->readValuesFromTable($table);
    }

    /**
     * @Given the list of :listCode:
     */
    public function readResourceLocalisedList(string $listCode, TableNode $table): void
    {
        $this->resourceData[$listCode] = $this->readLocalisedListFromTable($table);
    }

    /**
     * @Given /^associations:$/
     */
    public function readResourceAssociations(TableNode $table): void
    {
        $this->resourceData['associations'] = $this->readAssociationsFromTable($table);
    }

    /**
     * @Given with a text attribute :attributeName:
     */
    public function setTextAttributeValue(string $attributeName, PyStringNode $value)
    {
        $this->resourceData['values'][$attributeName][] = [
            'locale' => null,
            'scope' => null,
            'data' => $value->getRaw(),
        ];
    }

    /**
     * @Given I apply transformations using the profile:
     */
    public function initialiseEtlProfile(PyStringNode $string): void
    {
        $config = Yaml::parse($string);

        $factory = new EtlFactory($this->eventDispatcher);
        $profile = EtlProfile::fromArray($config);

        $this->transformer = $factory->createTransformer($profile);
    }

    /**
     * @When transformation is executed
     */
    public function transformationIsExecuted(): void
    {
        $resource = Resource::fromArray($this->resourceData, $this->resourceType);
        $this->extractor = new InMemoryExtractor($resource);

        $this->loader = new InMemoryLoader($resource);

        $etl = new EtlProcess(
            $this->extractor,
            $this->transformer,
            $this->loader,
            $this->eventDispatcher
        );

        $etl->execute();
    }

    /**
     * @Then the :resourceType in the PIM should have properties:
     */
    public function checkProperties(string $resourceType, TableNode $table): void
    {
        $expected = $this->readPropertiesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        unset($loaderData['values'], $loaderData['labels'], $loaderData['associations']);

        Assert::eq($expected, $loaderData);
    }

    /**
     * @Then should have attributes:
     */
    public function checkValues(TableNode $table): void
    {
        $expected = $this->readValuesFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();
        Assert::eq($expected, $loaderData['values']);
    }

    /**
     * @Then should have the list of :listCode:
     */
    public function checkLocalisedList(string $listCode, TableNode $table): void
    {
        $expected = $this->readLocalisedListFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();

        Assert::eq($expected, $loaderData[$listCode]);
    }

    /**
     * @Then should have associations:
     */
    public function checkAssociations(TableNode $table): void
    {
        $expected = $this->readAssociationsFromTable($table);

        $loaderData = $this->loader->getResult()->toArray();

        Assert::eq($expected, $loaderData['associations']);
    }

    /**
     * @Then should have the text attribute :attributeName:
     */
    public function checkTextAttributeValue(string $attributeName, PyStringNode $attributeValue)
    {
        $actualValue = $this->loader->getResult()->get(Attribute::create($attributeName, null, null));

        Assert::eq($attributeValue->getRaw(), $actualValue);
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

    private function readAssociationsFromTable(TableNode $table): array
    {
        $data = [];
        foreach ($table as $row) {
            $type = $row['type'];

            $data[$type] = [
                'products' => $this->convertTableValue($row['products']),
                'product_models' => $this->convertTableValue($row['product_models']),
                'groups' => $this->convertTableValue($row['groups']),
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
    }
}
