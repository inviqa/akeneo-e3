<?php

namespace AkeneoEtl\Application\Action;

use LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SetOptions
{
    private string $field;

    private ?string $locale;

    private ?string $scope;

    /**
     * @var mixed|null
     */
    private $value;

    private ?string $expression;

    /**
     * @param mixed $value
     */
    private function __construct(
        string $field,
        ?string $locale,
        ?string $scope,
        $value,
        ?string $expression
    ) {
        $this->field = $field;
        $this->locale = $locale;
        $this->scope = $scope;
        $this->value = $value;
        $this->expression = $expression;
    }

    public static function fromArray(array $array): self
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired('field')
            ->setDefined('scope')
            ->setDefined('locale')
            ->setDefined('value')
            ->setDefined('expression')
            ->setAllowedTypes('locale', ['string', 'null'])
            ->setAllowedTypes('scope', ['string', 'null'])
            ->setAllowedTypes('expression', ['string', 'null'])
            ->resolve($array);

        if (array_key_exists('value', $array) === false &&
            array_key_exists('expression', $array) === false) {
            throw new LogicException('One of the options `value` or `expression` are required for the set action.');
        }

        if (isset($array['value']) === true &&
            isset($array['expression']) === true) {
            throw new LogicException('Only one of the options `value` or `expression` is required for the set action.');
        }

        return new self(
            $array['field'],
            $array['locale'] ?? null,
            $array['scope'] ?? null,
            $array['value'] ?? null,
            $array['expression'] ?? null,
        );
    }

    public function getFieldName(): string
    {
        return $this->field;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getExpression(): ?string
    {
        return $this->expression;
    }
}
