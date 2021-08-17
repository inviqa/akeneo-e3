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

Before: <pre>yellow</pre>

After: <pre>magenta, pink</pre>
