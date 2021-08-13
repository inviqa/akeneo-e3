Feature: Data transformations using `set` actions
  As a user
  I want to change values of properties and attributes in Akeneo

  Scenario: Transform scalar values using fixed values:
  - change family
  - set parent to null
  - change name in existing locale
  - add name localisation

    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | hydra     |
      | parent     | akeneo    |
      | categories | [monster] |
    And attributes:
      | attribute | scope | locale | value     |
      | name      | web   | en_GB  | The Ziggy |
      | name      | web   | de_DE  | Der Ziggy |

    And I apply transformations using the profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: family
                  value: pet
              -
                  type: set
                  field: parent
                  value:
              -
                  type: set
                  field: name
                  scope: web
                  locale: de_DE
                  value: Die Ziggy
              -
                  type: set
                  field: name
                  scope: web
                  locale: uk_UA
                  value: Зіггі
      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | pet       |
      | parent     |           |
      | categories | [monster] |
    And should have attributes:
      | attribute | scope | locale | value     |
      | name      | web   | en_GB  | The Ziggy |
      | name      | web   | de_DE  | Die Ziggy |
      | name      | web   | uk_UA  | Зіггі     |


  Scenario: Transform scalar values using expressions:
  - change family (concatenate with a prefix)
  - upper case name in en_GB
  - add url_slug with a sluggified version of name

    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | hydra     |
      | categories | [monster] |
    And attributes:
      | attribute | scope | locale | value            |
      | name      | web   | en_GB  | The Ziggy        |
      | name      | web   | de_DE  | Dēr Süße $Zïggy$ |

    And I apply transformations using the profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: family
                  expression: 'family~"_new"'
              -
                  type: set
                  field: name
                  scope: web
                  locale: en_GB
                  expression: 'upperCase(value("name", "web", "en_GB"))'
              -
                  type: set
                  field: url_slug
                  scope: null
                  locale: de_DE
                  expression: 'lowerCase(slug(value("name", "web", "de_DE")))'

      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | hydra_new |
      | categories | [monster] |
    And should have attributes:
      | attribute | scope | locale | value            |
      | name      | web   | en_GB  | THE ZIGGY        |
      | name      | web   | de_DE  | Dēr Süße $Zïggy$ |
      | url_slug  |       | de_DE  | der-susse-ziggy  |


  Scenario: Transform array and object values using fixed values:
  - change categories
  - add label localisation

    Given an object in the PIM with properties:
      | field      | value     |
      | code       | ziggy     |
      | categories | [monster] |
    And the list of labels:
      | locale | value  |
      | en_GB  | Akeneo |
      | de_DE  | Akënëo |

    And I apply transformations using the profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: categories
                  value: ['pet', 'pim']
              -
                  type: set
                  field: labels
                  value:
                      de_DE: Akeneö
                      uk_UA: Акенео
      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | code       | ziggy     |
      | categories | [pet,pim] |
    And should have the list of labels:
      | locale | value  |
      | en_GB  | Akeneo |
      | de_DE  | Akeneö |
      | uk_UA  | Акенео |


  Scenario: Add elements to product associations

    Given a product in the PIM with properties:
      | field      | value |
      | identifier | ziggy |
    And associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | [izzy]   | []             | []     |

    And I apply transformations using the profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: associations
                  value:
                      FRIENDS:
                          products: ['gizzy', 'jazzy']
                      RELATIVES:
                          product_models: ['unicorn', 'mermaid']
                          groups: ['magical_creatures']

      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value |
      | identifier | ziggy |
    And should have associations:
      | type      | products      | product_models    | groups              |
      | FRIENDS   | [gizzy,jazzy] | []                | []                  |
      | RELATIVES | [izzy]        | [unicorn,mermaid] | [magical_creatures] |
