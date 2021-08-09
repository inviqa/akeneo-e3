# Use cases to be tested with ETL

- :robot: - automated acceptance test created
- :man: - manually tested

## Product / SET action

| :robot: | :man: | Fields     | 
| ---- | ---- | ---------- | 
| :ok: | :ok: | scalar: family, parent | 
|  |  | scalar: identifier | 
| :ok: | :ok: | array: categories | 
| :ok: | :ok: | array: associations | 
|  |  | array(?): quantified associations (Akeneo 5) | 

## Category / SET action

| :robot: | :man: | Fields     |
| ---- | ---- | ---------- | 
|  |  | scalar: parent | 
| :ok: | :ok: | object: labels | 
