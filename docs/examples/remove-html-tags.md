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

Before: <pre> &lt;p&gt;Lorem ipsum &lt;span&gt;dolor sit amet&lt;span&gt;.&lt;/p&gt;</pre>

After: <pre>Lorem ipsum dolor sit amet.</pre>
