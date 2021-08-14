<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression\Functions;

use AkeneoEtl\Application\Expression\StateHolder;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Attribute;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

function slug(string $string, string $separator = '-', string $locale = null): string
{
    $slugger = new AsciiSlugger();

    return $slugger->slug($string, $separator, $locale)->toString();
}

function lowerCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->lower()->toString();
}

function upperCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->upper()->toString();
}

function camelCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->camel()->toString();
}

function snakeCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->snake()->toString();
}

function trim(string $string, string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->trim($chars)->toString();
}

/**
 * Returns a value of an attribute by name, channel and locale.
 * If name is not specified, it returns a value of a field from the current rule.
 *
 * @return mixed|null
 */
function value(string $name = '', ?string $channel = null, ?string $locale = null)
{
    $resource = StateHolder::$resource;

    $field = ($name === '') ?
        StateHolder::$field :
        Attribute::create($name, $channel, $locale);

    if ($resource->has($field) === false) {
        throw new TransformException(sprintf('Attribute %s is not present in data', $name), true);
    }

    return $resource->get($field);
}

function removeHtmlTags(string $string, array $allowedTags = []): string
{
    return trim(strip_tags($string, $allowedTags));
}
