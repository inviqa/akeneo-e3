[comment]: <> (This file is auto-generated based on example-provider.)
# How to remove HTML tags

**Task:** remove HTML tags from a `description` attribute.

### Rules

```yaml
actions:
    -
        type: set
        field: description
        scope: null
        locale: en_GB
        expression: removeHtmlTags(value())
```

### Result

Field: `description`

Before applying actions: <pre> &lt;p&gt;Lorem ipsum &lt;span&gt;dolor sit amet&lt;span&gt;.&lt;/p&gt;</pre>

After applying actions: <pre>Lorem ipsum dolor sit amet.</pre>
