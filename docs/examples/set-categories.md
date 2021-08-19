[comment]: <> (This file is auto-generated based on example-provider.)
# Modify categories of a product or a product model

## Task: set `categories`

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

**Task:** add new items to `categories`

### Rules

```yaml
actions:
    -
        type: add
        field: categories
        items:
            - pxm
            - akeneo
```

### Result

Field: `categories`

Before applying actions: <pre>pim, pet</pre>

After applying actions: <pre>pxm, akeneo</pre>
**Task:** add new items to `categories` using an expression

### Rules

```yaml
actions:
    -
        type: add
        field: categories
        expression: '[''pxm'', upperCase(identifier)]'
```

### Result

Field: `categories`

Before applying actions: <pre>pim, pet</pre>

After applying actions: <pre>pxm, THE-ZIGGY</pre>
