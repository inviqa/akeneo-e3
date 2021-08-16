# Set categories of a product or a product model


**Task:** set value of a `categories` field

### Rules

```yaml
actions:
    -
        type: set
        field: categories
        value:
            - pxm
            - akeneo
```

### Result

Field: `categories`

Before: <pre>pim, pet</pre>

After: <pre>pxm, akeneo</pre>
