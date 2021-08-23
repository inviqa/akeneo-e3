# Rule actions

Akeneo E3 is compatible with [Akeneo Enrichment Rules](https://docs.akeneo.com/master/manipulate_pim_data/rule/general_information_on_rule_format.html).

You may copy your existing enrichment rules and use them with E3.


## Supported actions

### set

This action assigns values to fields or attributes.

Akeneo E3 supports expressions for this action.


### add

This action allows adding values to a multi-select attribute, 
a reference entity multiple links attribute or a product to categories or groups.

Akeneo E3 supports expressions for this action.

### remove

This action removes values from a multi-select attribute, a reference entity multiple links attribute or a product category.

Akeneo E3 supports expressions for this action.

## Actions that are differently implemented in E3  

### copy

E3 supports copying one value to another using a `set` action with an expression. 
For example, copy a product `name` to `description`:

```yaml
actions:
    -
        type: set
        field: description
        scope: web
        locale: en_GB
        expression: 'value("name", "web", "en_GB")'
```

### concatenate

E3 supports concatenation using a `set` action with an expression.
For example, concatenate `description` using values from `name`, `brand` and `model` attributes:

```yaml
actions:
    -
        type: set
        field: description
        scope: web
        locale: en_GB
        expression: 'value("name", "web", "en_GB") ~ " / " ~ value("brand", null, null) ~ " / " ~ value("model", null, null)'
```

### concatenate

TBD

### calculate

E3 supports calculation using a `set` action with an expression. 
And this is much easier than a syntax of Akeneo rules ;)

For example, calculate a `valume` using values from `height` and `radius` attributes:

```yaml
actions:
    -
        type: set
        field: volume
        scope: null
        expression: '3.14 * value("height") * (value("radius") ** 2)'
```

