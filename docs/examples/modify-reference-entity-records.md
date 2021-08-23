[comment]: <> (This file is auto-generated based on example-provider.)
# Modify reference entity records

## Task: trim values of a `description` attribute of all `suppliers` reference entity records.

### Rules

```yaml
conditions:
    -
        field: reference_entity_code
        value: suppliers
actions:
    -
        type: set
        field: description
        scope: null
        locale: de_DE
        expression: trim(value())
```

### Result

Field: `description`

Before applying actions: <pre>  I&#039;m a Ziggy-supplier    </pre>

After applying actions: <pre>I&#039;m a Ziggy-supplier</pre>
