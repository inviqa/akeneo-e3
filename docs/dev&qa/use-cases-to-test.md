# Test cases

:robot: - automated acceptance test created

:man: - manually tested

## SET action 

### Product / product model / reference entities

| Fields                    | Merge type        | Who tested    | Example 
| ------------------------- | ----------------- | ------------- | ------- 
| properties e.g. family    | scalar            | :robot: :man: |  
| identifier                | scalar            |               | 
| categories                | array             | :robot: :man: | 
| values: scalar attributes | object            | :robot: :man: | 
| values: number            | object            |               | 
| values: price             | object            | :man:         | set-price.yaml 
| values: metric            | object            | :man:         | set-metric.yaml 
| values: multi-select      | object            | :man:         | set-multi-select.yaml 
| values: asset collection  | object            | :man:         | set-asset-collection.yaml
| associations              | object+array      | :robot: :man: | 
| quantified associations   | object+array+obj. |               | 

## Additional cases

| Fields                         | Merge type  | Who tested    |
| ------------------------------ | ----------- | ------------- | 
| category labels                | array       |  |
| family: attribute_requirements | array       |  |
| attribute group: ...           | array       |  |
