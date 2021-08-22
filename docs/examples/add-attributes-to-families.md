[comment]: <> (This file is auto-generated based on example-provider.)
# Add attributes to families

**Task:** add an attribute to families.

### Rules

```yaml
actions:
    -
        type: add
        field: attributes
        items:
            - colour
```

### Result

Field: `attributes`

Before applying actions: <pre>sku, name, description</pre>

After applying actions: <pre>sku, name, description, colour</pre>
**Task:** add an attribute to families and set it as required for the channel `ecommerce`.

### Rules

```yaml
actions:
    -
        type: add
        field: attributes
        items:
            - colour
    -
        type: add
        field: attribute_requirements
        items:
            ecommerce: [colour]
```

### Result

Field: `attributes`

Before applying actions: <pre>sku, name, description</pre>

After applying actions: <pre>sku, name, description, colour</pre>
Field: `attribute_requirements`

Before applying actions: <pre>ecommerce: sku, name</pre>

After applying actions: <pre>ecommerce: sku, name, colour</pre>
