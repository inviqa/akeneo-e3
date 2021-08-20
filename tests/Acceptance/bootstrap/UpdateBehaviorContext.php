<?php

namespace AkeneoEtl\Tests\Acceptance\bootstrap;

use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\AuditableResource;
use AkeneoEtl\Domain\Resource\NonAuditableResource;
use AkeneoEtl\Domain\Resource\Property;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Exception;
use Webmozart\Assert\Assert;

class UpdateBehaviorContext implements Context
{
    private AuditableResource $resource;

    private string $resourceType;

    /**
     * @Given an original resource:
     */
    public function readOriginalResource(PyStringNode $json): void
    {
        $data = json_decode($json, true);
        $this->resourceType = isset($data['identifier']) ? 'product' : 'object';

        $this->resource = AuditableResource::fromArray($data, $this->resourceType);
    }

    /**
     * @When I apply the patch request body
     */
    public function applyPatchRequestBody(PyStringNode $json): void
    {
        $patch = json_decode($json, true);

        try {
            $attributeValues = $patch['values'] ?? [];
            $this->setAttributeValues($attributeValues);
            unset($patch['values']);
            $this->applyPropertyValues($patch);
        } catch (TransformException $e) {
            if ($e->canBeSkipped() === true) {
                return;
            }
        }
    }

    /**
     * @Then the resulting resource should be:
     */
    public function theResultingResourceShouldBe(PyStringNode $json): void
    {
        $expected = json_decode($json, true);

        Assert::eq($expected, $this->resource->toArray());
    }

    private function setAttributeValues(array $attributeValues): void
    {
        foreach ($attributeValues as $fieldName => $values) {
            foreach ($values as $value) {
                $this->resource->set(
                    Attribute::create(
                        $fieldName,
                        $value['scope'],
                        $value['locale']
                    ),
                    $value['data']
                );
            }
        }
    }

    private function applyPropertyValues(array $properties): void
    {
        foreach ($properties as $fieldName => $value) {
            $this->resource->set(Property::create($fieldName), $value);
        }
    }
}
