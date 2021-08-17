[comment]: <> (This file is auto-generated based on example-provider.)
# How to trim values

**Task:** trim values of a `name` attribute of `en_GB` locale.

### Rules

```yaml
actions:
    -
        type: set
        field: name
        scope: web
        locale: en_GB
        expression: 'trim(value("name", "web", "en_GB"))'
```

### Result

Field: `name`

Before applying actions: <pre>    The Ziggy        </pre>

After applying actions: <pre>The Ziggy</pre>
