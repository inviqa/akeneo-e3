[comment]: <> (This file is auto-generated based on example-provider.)
# Set value of an asset collection type attribute

**Task:** set value of an asset collection type attribute `images`

### Rules

```yaml
actions:
    -
        type: set
        field: images
        locale: null
        scope: null
        value:
            - asset_image_code2
            - asset_image_code3
```

### Result

Field: `images`

Before applying actions: <pre>asset_image_code1</pre>

After applying actions: <pre>asset_image_code2, asset_image_code3</pre>
