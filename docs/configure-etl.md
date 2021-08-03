# How to configure data manipulation

ETL stands for Extract -> Transform -> Load.
Therefore, the configuration consists of 3 sections:

* `extract` (optional)
* `transform`
* `load` (optional)


## extract (optional)

It configures how to fetch data from your Akeneo instance.
The format of this section resembles Akeneo rule format.

Example:

```yaml
extract:
    conditions:
        -
            field: 'identifier'
            operator: 'IN'
            value: ['a123', 'b234']
```

Applies to: product, product-model (to do, list all filterable resources).

If you omit this section, Akeneo ETL will fetch all data without filters.   

## Transform

to do

## Load

to do

