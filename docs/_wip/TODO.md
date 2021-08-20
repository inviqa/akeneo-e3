# TO DO

## Transformer mode

- [x] patch
- [ ] copy (full?)

## Transform actions

- [x] set
- [ ] add ([akeneo rule engine add](https://docs.akeneo.com/5.0/manipulate_pim_data/rule/general_information_on_rule_format.html#add))   
- [ ] remove ([akeneo rule engine remove](https://docs.akeneo.com/5.0/manipulate_pim_data/rule/general_information_on_rule_format.html#remove))   
- [ ] concatenate :question: ([akeneo rule engine concatenate](https://docs.akeneo.com/5.0/manipulate_pim_data/rule/general_information_on_rule_format.html#concatenate)) - expressions are better here
- [ ] clear :question: ([akeneo rule engine clear](https://docs.akeneo.com/5.0/manipulate_pim_data/rule/general_information_on_rule_format.html#clear)) - set to null?
- [ ] calculate :question: ([akeneo rule engine calculate](https://docs.akeneo.com/5.0/manipulate_pim_data/rule/general_information_on_rule_format.html#calculate)) - expressions are **much** better here
- [ ] duplicate? no
- [ ] transform-to? another resource type (smart transform of attributes?)
- [ ] change-resource-type / change-type


### Supported data types / features

- [ ] all from api
- [ ] accumulating APIs, e.g. apply rules to **all** ref entity records (need to first fetch all ref entites and iterate them) 


## Expression language

### Functions

- [x] value (attributeValue() ?)
- [x] strip html tags
- [ ] string functions  : replace, etc - how to expose all UnicodeString stuff?
- [ ] array functions: merge, search, etc.
- [x] hasAttribute(name, scope, locale) - hasAttribute
- [x] hasProperty(name)
- [ ] priceValue(name, scope, locale, currency)
- [ ] metricValue(name, scope, locale, unit)
- [ ] lastRunDateTime(runId)   


 
## Commands

- [x] transform
- [ ] transform set - no profile, only command options: `set --field=name --scope=de_DE --value="new name"`
- [ ] transform copy (migrate) - no ETL config, only command options: `copy --types=attribute,family,product`
- [ ] generate connection profile
- [ ] validate connection profile
- [ ] generate ETL profile :question:
- [ ] validate ETL profile against schema (optionresolver)
- [ ] validate ETL profile against Akeneo (do attributes exist, are they configured accordingly, e.g. scopable if scope configured)
- [ ] condition-based DELETE (products, attributes, etc.)  

## More ideas

For expressions: object Akeneo to access real data, 
e.g. attribute options or ref. entities
