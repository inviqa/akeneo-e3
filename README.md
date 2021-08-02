# Akeneo ETL

Akeneo ETL is a tool to manipulate product data in the Akeneo PIM using its REST API.

## When to use

* To change product attribute values in bulk, e.g. trim data, remove html tags, lowercase or uppercase.
* To help with attribute type changes, e.g. to copy values of a non-localisable attribute to a localisable one.
* To migrate data between instances, e.g. copy a range products from UAT to production.  

## How to use

Step 1. Create a connection in Akeneo or use one of existing connections.


Step 2. Create a connection profile.

Create a file connection.yaml and configure it using your connection credentials.

```yaml
host: '{{ Your Akeneo server url }}'
clientId: '{{ Client ID}}'
clientSecret: '{{ Secret }}'
userName: ' {{ Username }}'
userPassword: '{{ Password }}'
```

Step 3. Generate an ETL profile (specification of your transformations).

Create a file etl.yaml, e.g. like this:

```yaml

# How to retrieve data from Akeneo
extract:
# How to transform data
transform:
    steps:
        # Set a value of a `slug` field with a slugified lowercased value of the name field from the de_DE locale 
        -
            type: set
            field: slug
            locale: de_DE
            scope: null
            value: 'lowercase(slug(value(values, "name", null, "de_DE")))'

# How to save data
load:
```

Step 4. Run the script
```bash
bin/akeneo-etl.php e:p --connection-profile=connection.yaml --etl-profile=etl.yaml
```
