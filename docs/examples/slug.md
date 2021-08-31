[comment]: <> (This file is auto-generated based on example-provider.)
# How to generate slugs (e.g. product urls)

## Task: populate an `url_slug` field as a slug of the `name` attribute value.

### Rules

```yaml
actions:
    -
        type: set
        field: url_slug
        scope: web
        locale: en_GB
        expression: 'lowerCase(slug(value("name", "web", "en_GB")))'
```

### Result

Field: `url_slug`

Before applying actions: <pre></pre>

After applying actions: <pre>ziggy-the-hydra</pre>
