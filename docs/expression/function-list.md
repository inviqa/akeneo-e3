[comment]: <> (This file is auto-generated based on the list of functions registered in ExpressionLanguage.)
# Expression functions

Expressions allow manipulating data using functions, like in Excel/Google sheets.


## value

Returns a value of an attribute by `name`, `channel` and `locale`.


Examples:

 * `value("name", null, "en_GB")` => `Ziggy`
 * `value("description", "web", "en_GB")` => `Ziggy The Hydra`


If `name` is not specified, it returns a value of a field from the current rule.

E.g. if a current rule action is:

```
-
     type: set
     field: name
     scope: web
     locale: en_GB
```
then
   <pre>expression: 'value()'</pre>
is as same as
   <pre>expression: 'value("name", "web", "en_GB")'</pre>


## hasAttribute

Returns true if an attribute with the given `name`, `channel` and `locale` exists in data.


Examples:

 * `hasAttribute("name", null, "en_GB")` => `true`
 * `hasAttribute("~unknown~", "web", "en_GB")` => `false`


| Parameter | Description | Type
| --------- | ----------- | ----
| name |  | string
| channel |  | string/null
| locale |  | string/null

If `name` is not specified, it checks for the attribute from the current rule.

E.g. if a current rule action is:

```
-
     type: set
     field: name
     scope: web
     locale: en_GB
```
then
   <pre>expression: 'hasAttribute()'</pre>
is as same as
   <pre>expression: 'hasAttribute("name", "web", "en_GB")'</pre>


## hasProperty

Returns true if a property with the given `name` exists in data.


Examples:

 * `hasProperty("family")` => `true`
 * `hasProperty("~unknown~")` => `false`


| Parameter | Description | Type
| --------- | ----------- | ----
| name |  | string

If `name` is not specified, it checks for the property from the current rule.

E.g. if a current rule action is:

```
-
     type: set
     field: family
```
then
   <pre>expression: 'hasProperty()'</pre>
is as same as
   <pre>expression: 'hasProperty("family")'</pre>


## removeHtmlTags

Remove HTML tags from a `string`


Examples:

 * `removeHtmlTags("<p>Lorem ipsum <span>dolor sit amet<span>.</p>")` => `Lorem ipsum dolor sit amet.`
 * `removeHtmlTags("<p>Lorem ipsum <span>dolor sit amet<span>.</p>", ["span"])` => `Lorem ipsum <span>dolor sit amet<span>.`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string
| allowedTags | Tags which should not be removed | array




## trim

Removes whitespaces (or other `characters`) from the beginning and end of a `string`


Examples:

 * `trim("     How To Raise A Ziggy  ")` => `How To Raise A Ziggy`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string
| chars | Characters to remove, by default all whitespaces &quot; \t\n\r\0\x0B\x0C\u{A0}\u{FEFF}&quot; | string




## slug

Generates a slug for a `string`.


Examples:

 * `slug("How To Raise A Ziggy")` => `How-To-Raise-A-Ziggy`
 * `slug("How To Raise A Ziggy", "_")` => `How_To_Raise_A_Ziggy`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string
| separator | Word separator, default: &#039;-&#039; | string
| locale | Locale, e.g. en_GB, de_DE, es_ES | string/null




## replace

Replaces `from` with `to` in a `string`


Examples:

 * `replace("Lorem\nipsum\ndolor\nsit\namet", "\n", " ")` => `Lorem ipsum dolor sit amet`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string
| from |  | string
| to |  | string




## lowerCase

Make a `string` lowercase


Examples:

 * `lowerCase("How To Raise A Ziggy")` => `how to raise a ziggy`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string




## upperCase

Make a `string` UPPERCASE


Examples:

 * `upperCase("How To Raise A Ziggy")` => `HOW TO RAISE A ZIGGY`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string




## camelCase

Make a `string` camelCase


Examples:

 * `camelCase("How To Raise A Ziggy")` => `howToRaiseAZiggy`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string




## snakeCase

Make a `string` snake_case


Examples:

 * `snakeCase("How To Raise A Ziggy")` => `how_to_raise_a_ziggy`


| Parameter | Description | Type
| --------- | ----------- | ----
| string |  | string




