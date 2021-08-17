[comment]: <> (This file is auto-generated based on example-provider.)
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

Before applying actions: <pre>pim, pet</pre>

After applying actions: <pre>pxm, akeneo</pre>
