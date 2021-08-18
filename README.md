# Enhanced Enrichment Engine (E3) for Akeneo PIM

Enhanced Enrichment Engine is a tool to manipulate product data in the Akeneo PIM using its REST API.

It is similar and compatible with Akeneo Enrichment Rules but is more powerful. See how it is [different from Akeneo Rules](docs/compare-with-akeneo-rules.md).

## When to use E3

:star:&nbsp;**Data cleansing** e.g. 
* [trim attribute values](docs/examples/trim.md)
* [remove html tags](docs/examples/remove-html-tags.md)
* [remove artefacts from text attributes](docs/examples/replace.md)

:star:&nbsp;**Improve data** e.g.
* lowercase or uppercase attribute values
* generate slugs

:star:&nbsp;**Attribute type changes** e.g.
* copy values of a non-localisable attribute to a localisable one
* copy number data to a metric attribute

:star:&nbsp;**Data model changes** e.g.
* add an attribute to all families

:star:&nbsp;**Migration** e.g. 
* copy a product range from UAT to production  

## How to use E3

Step 1. Create a connection in Akeneo or use one of existing connections.

Step 2. Create a connection profile.

Create a file `connection.yaml` and configure it using your connection credentials.

```yaml
host: '{{ Your Akeneo server url }}'
clientId: '{{ Client ID}}'
clientSecret: '{{ Secret }}'
userName: ' {{ Username }}'
userPassword: '{{ Password }}'
```

Step 3. Define your rules

Create a file `rules.yaml`, e.g. this configuration allows you to trim values of the `name` attribute for the `en_GB` locale:

```yaml
actions:
    -
        type: set
        field: name
        locale: en_GB
        scope: null
        expression: 'trim(value("name", null, "en_GB"))'
```

See also 
 * [examples of rules](docs/example-list.md) 
 * [available expression functions](docs/function-list.md).


Step 4. Run the script:
```bash
bin/akeneo-etl transform --resource-type=product --connection=connection.yaml --profile=rules.yaml
```
