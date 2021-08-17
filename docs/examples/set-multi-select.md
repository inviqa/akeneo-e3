[comment]: <> (This file is auto-generated based on example-provider.)
# Set value of a multi-select type attribute

**Task:** set value of a multi-select type attribute `colours`

### Rules

```yaml
actions:
    -
        type: set
        field: colours
        locale: null
        scope: null
        value:
            - magenta
            - pink
```

### Result

Field: `colours`

Before applying actions: <pre>yellow</pre>

After applying actions: <pre>magenta, pink</pre>
