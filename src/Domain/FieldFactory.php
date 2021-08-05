<?php

namespace AkeneoEtl\Domain;

class FieldFactory
{
    public static function fromOptions(array $options): Field
    {
        if (array_key_exists('scope', $options) === false && array_key_exists('locale', $options) === false) {
            return Property::create($options['field']);
        }

        return Attribute::create($options['field'], $options['scope'] ?? null, $options['locale'] ?? null);
    }
}
