[comment]: <> (This file is auto-generated based on example-provider.)
# How to replace strings in values

**Task:** replace all `\n` occurrences with spaces.

### Rules

```yaml
actions:
    -
        type: set
        field: description
        scope: web
        locale: en_GB
        expression: 'replace(value(), "\\n", " ")'
```

### Result

Field: `description`

Before applying actions: <pre>Lorem\nipsum\ndolor\nsit\namet</pre>

After applying actions: <pre>Lorem ipsum dolor sit amet</pre>
