<?php

namespace AkeneoEtl\Application\Expression\Functions;

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

function value(array $values, string $name, ?string $channel, ?string $locale)
{
    foreach ($values[$name] as $attributeValue) {
        if ($attributeValue['scope'] === $channel &&
            $attributeValue['locale'] === $locale) {
            return $attributeValue['data'];
        }
    }

    return null;
}
