<?php

namespace AkeneoEtl\Application\Expression\Functions;

use AkeneoEtl\Domain\Attribute;
use AkeneoEtl\Domain\CurrentResourceHolder;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

function slug(string $string, string $separator = '-', string $locale = null): string
{
    $slugger = new AsciiSlugger();

    return $slugger->slug($string, $separator, $locale);
}

function lowercase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->lower();
}

function uppercase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->upper();
}

function camelcase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->camel();
}

function snakecase(string $string): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->snake();
}

function trim(string $string, string $chars = " \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}"): string
{
    $unicodeString = new UnicodeString($string);

    return $unicodeString->trim($chars);
}

/**
 * @param mixed|null $defaultValue
 *
 * @return mixed|null
 */
function value(string $name, ?string $channel, ?string $locale, $defaultValue = null)
{
    $resource = CurrentResourceHolder::$current;

    $field = Attribute::create($name, $channel, $locale);

    return $resource->get($field, $defaultValue);
}
