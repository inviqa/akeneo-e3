[comment]: <> (This file is auto-generated based on example-provider.)
# Modify associations

## Task: set `associations` to products or product models.

### Rules

```yaml
actions:
    -
        type: set
        field: associations
        value:
            FRIENDS: { products: [gizzy, jazzy] }
            RELATIVES: { product_models: [mermaid] }
```

### Result

Field: `associations`

Before applying actions: <pre>FRIENDS.products: buzzy
FRIENDS.product_models: 
FRIENDS.groups: 
RELATIVES.products: 
RELATIVES.product_models: unicorn
RELATIVES.groups: </pre>

After applying actions: <pre>FRIENDS.products: gizzy, jazzy
FRIENDS.product_models: 
FRIENDS.groups: 
RELATIVES.products: 
RELATIVES.product_models: mermaid
RELATIVES.groups: </pre>
## Task: add new `associations` to products or product models.

### Rules

```yaml
actions:
    -
        type: add
        field: associations
        items:
            FRIENDS: { products: [gizzy, jazzy] }
            RELATIVES: { product_models: [mermaid] }
            NEW: { groups: [magical_creatures] }
```

### Result

Field: `associations`

Before applying actions: <pre>FRIENDS.products: buzzy
FRIENDS.product_models: 
FRIENDS.groups: 
RELATIVES.products: 
RELATIVES.product_models: unicorn
RELATIVES.groups: 
NEW.groups: magical_creatures</pre>

After applying actions: <pre>FRIENDS.products: buzzy, gizzy, jazzy
FRIENDS.product_models: 
FRIENDS.groups: 
RELATIVES.products: 
RELATIVES.product_models: unicorn, mermaid
RELATIVES.groups: 
NEW.groups: magical_creatures</pre>
