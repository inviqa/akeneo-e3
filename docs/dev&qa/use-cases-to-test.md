# Use cases to be tested with ETL

:robot: - automated acceptance test created

:man: - manually tested

## Product / SET action

| :robot: | :man: | Fields     | Merge type |
| ---- | ---- | ---------- | ---------- |
| :ok: | :ok: | family, parent | scalar | 
|      |      | identifier    | scalar |
| :ok: | :ok: | categories | array |
| :ok: | :ok: | values: scalar attributes | object |
|      |      | values: number | object |
|      |      | values: price | object |
|      |      | values: metric | object |
|      |      | values: multi-select | object |
|      |      | values: asset collection | object |
| :ok: | :ok: | associations | object+array |
|      |      | quantified associations | object+array+object |

## Category / SET action

| :robot: | :man: | Fields | Merge type |
| ---- | ---- | ---------- | ---------- |
|      |      | parent | scalar | 
| :ok: | :ok: | labels | object | 

## Family / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 
|         |       | attribute_requirements             | object+array     | 


## Family variant / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     |

## Attribute / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 

## Attribute group / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 
|         |       | attributes | array     | 


## Attribute option / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 

## Reference entity / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 


## Reference entity record / SET action

| :robot: | :man: | Fields       | Merge type |
| ------- | ----- | ------------ | ---------- |
|         |       |              | scalar     | 
