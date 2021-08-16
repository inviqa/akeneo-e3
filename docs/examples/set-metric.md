# Set value of a metric type attribute


**Task:** set value of a metric attribute `gross_weight`

### Rules

```yaml
actions:
    -
        type: set
        field: gross_weight
        locale: null
        scope: null
        value:
            amount: '2.5'
            unit: KILOGRAM
```

### Result

Field: `gross_weight`

Before: <pre>amount: 200
unit: GRAM</pre>

After: <pre>amount: 2.5
unit: KILOGRAM</pre>
