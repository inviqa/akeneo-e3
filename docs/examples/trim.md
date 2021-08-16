# How to trim values


Task: trim values of a `name` attribute of `en_GB` locale.

Rules:

```yaml
actions:
    -
        type: set
        field: name
        scope: web
        locale: en_GB
        expression: 'trim(value("name", "web", "en_GB"))'
```

Result:

Field: name

Before: `    The Ziggy        `

After: `The Ziggy`
