Feature: Data transformations using Akeneo-ETL

  Scenario: Transform scalar values with "set" actions using fixed values:
  - change family
  - set parent to null
  - change name in en_GB
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

    And an ETL profile:
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
                  locale: ua_UA
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
      | name      | web   | ua_UA  | Зіггі     |

  Scenario: Transform scalar values with "set" actions using expressions:
  - change family (concatenate with a prefix)
  - uppercase name in en_GB
  - add url_slug

    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | hydra     |
      | categories | [monster] |
    And attributes:
      | attribute | scope | locale | value            |
      | name      | web   | en_GB  | The Ziggy        |
      | name      | web   | de_DE  | Dēr Süße $Zïggy$ |

    And an ETL profile:
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
                  expression: 'uppercase(value("name", "web", "en_GB"))'
              -
                  type: set
                  field: url_slug
                  scope: null
                  locale: de_DE
                  expression: 'lowercase(slug(value("name", "web", "de_DE")))'

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

    And an ETL profile:
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
                      ua_UA: Акенео
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
      | ua_UA  | Акенео |