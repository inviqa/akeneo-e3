<?php

declare(strict_types=1);

namespace AkeneoEtl\Application\Expression\Functions;

use AkeneoEtl\Application\Expression\StateHolder;
use AkeneoEtl\Domain\Exception\TransformException;
use AkeneoEtl\Domain\Resource\Attribute;
use AkeneoEtl\Domain\Resource\Property;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

/**
 * Generates a slug for a `string`.
 *
 * @param string $string
 * @param string $separator Word separator, default: '-'
 * @param string|null $locale Locale, e.g. en_GB, de_DE, es_ES
 *
 * @meta-arguments "How To Raise A Ziggy"
 * @meta-arguments "How To Raise A Ziggy", "_"
 *
 * @return string
 */
function slug(string $string, string $separator = '-', string $locale = null): string
{
    $slugger = new AsciiSlugger();

    return $slugger->slug($string, $separator, $locale)->toString();
}

/**
 * Make a `string` lowercase
 *
 * @param string $string
 *
 * @meta-arguments "How To Raise A Ziggy"
 *
 * @return string
 */
function lowerCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->lower()->toString();
}

/**
 * Make a `string` UPPERCASE
 *
 * @param string $string
 *
 * @meta-arguments "How To Raise A Ziggy"
 *
 * @return string
 */
function upperCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->upper()->toString();
}

/**
 * Make a `string` camelCase
 *
 * @param string $string
 *
 * @meta-arguments "How To Raise A Ziggy"
 *
 * @return string
 */
function camelCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->camel()->toString();
}

/**
 * Make a `string` snake_case
 *
 * @param string $string
 *
 * @meta-arguments "How To Raise A Ziggy"
 *
 * @return string
 */
function snakeCase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->snake()->toString();
}

/**
 * Removes whitespaces (or other `characters`) from the beginning and end of a `string`
 *
 * @param string $string
 * @param string $chars Characters to remove, by default all whitespaces " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"
 *
 * @return string
 *
 * @meta-arguments "     How To Raise A Ziggy  "
 */
function trim(string $string, string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->trim($chars)->toString();
}

/**
 * Returns a value of an attribute by `name`, `channel` and `locale`.
 *
 * If `name` is not specified, it returns a value of a field from the current rule.
 *
 * E.g. if a current rule action is:
 *
 * ```
 * -
 *      type: set
 *      field: name
 *      scope: web
 *      locale: en_GB
 * ```
 * then
 *    <pre>expression: 'value()'</pre>
 * is as same as
 *    <pre>expression: 'value("name", "web", "en_GB")'</pre>
 *
 * @meta-arguments "name", null, "en_GB"
 * @meta-arguments "description", "web", "en_GB"
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
        throw new TransformException(sprintf('Attribute %s is not present in data', $field->getName()), true);
    }

    return $resource->get($field);
}

/**
 * Returns true if an attribute with the given `name`, `channel` and `locale` exists in data.
 *
 * If `name` is not specified, it checks for the attribute from the current rule.
 *
 * E.g. if a current rule action is:
 *
 * ```
 * -
 *      type: set
 *      field: name
 *      scope: web
 *      locale: en_GB
 * ```
 * then
 *    <pre>expression: 'hasAttribute()'</pre>
 * is as same as
 *    <pre>expression: 'hasAttribute("name", "web", "en_GB")'</pre>
 *
 * @meta-arguments "name", null, "en_GB"
 * @meta-arguments "description", "web", "en_GB"
 *
 * @param string $name
 * @param string|null $channel
 * @param string|null $locale
 *
 * @return bool
 */
function hasAttribute(string $name = '', ?string $channel = null, ?string $locale = null): bool
{
    $resource = StateHolder::$resource;

    $field = ($name === '') ?
        StateHolder::$field :
        Attribute::create($name, $channel, $locale);

    if (!$field instanceof Attribute) {
        throw new TransformException(sprintf('Current field %s is not an attribute', $field->getName()), true);
    }

    return $resource->has($field);
}

/**
 * Returns true if a property with the given `name` exists in data.
 *
 * If `name` is not specified, it checks for the property from the current rule.
 *
 * E.g. if a current rule action is:
 *
 * ```
 * -
 *      type: set
 *      field: family
 * ```
 * then
 *    <pre>expression: 'hasProperty()'</pre>
 * is as same as
 *    <pre>expression: 'hasProperty("family")'</pre>
 *
 * @meta-arguments "family"
 * @meta-arguments
 *
 * @param string $name
 *
 * @return bool
 */
function hasProperty(string $name = ''): bool
{
    $resource = StateHolder::$resource;

    $field = ($name === '') ?
        StateHolder::$field :
        Property::create($name);

    if (!$field instanceof Property) {
        throw new TransformException(sprintf('Current field %s is not a property', $field->getName()), true);
    }

    return $resource->has($field);
}

/**
 * Remove HTML tags from a `string`
 *
 * @param string $string
 * @param array $allowedTags Tags which should not be removed
 *
 * @meta-arguments "<p>Lorem ipsum <span>dolor sit amet<span>.</p>"
 * @meta-arguments "<p>Lorem ipsum <span>dolor sit amet<span>.</p>", ["span"]
 *
 * @return string
 */
function removeHtmlTags(string $string, array $allowedTags = []): string
{
    return trim(strip_tags($string, $allowedTags));
}
