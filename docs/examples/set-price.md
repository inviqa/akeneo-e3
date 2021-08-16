# Set values of price attributes


**Task:** set value of a price attribute `price_initial`

### Rules

```yaml
actions:
    -
        type: set
        field: price_initial
        locale: en_GB
        scope: null
        value:
            - { amount: '12.34', currency: EUR }
            - { amount: '14.00', currency: USD }
```

### Result

Field: `price_initial`

Before: <pre>amount: 10.34
currency: EUR</pre>

After: <pre>amount: 12.34
currency: EUR
amount: 14.00
currency: USD</pre>
