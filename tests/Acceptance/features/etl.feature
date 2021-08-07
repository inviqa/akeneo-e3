Feature: Data transformations using Akeneo-ETL

  Scenario: Transform scalar values with a series of "set" actions using fixed values:
  - change family
  - change name in en_GB
  - add a de_DE localisation for name

    Given a product in the PIM:
      |   | field      | scope | locale | value     |
      |   | identifier |       |        | ziggy     |
      |   | family     |       |        | hydra     |
      |   | categories |       |        | [monster] |
      | * | name       | web   | en_GB  | The Ziggy |
      | * | name       | web   | de_DE  | Der Ziggy |

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
    Then the product in the PIM should look like:
      |   | field      | scope | locale | value     |
      |   | identifier |       |        | ziggy     |
      |   | family     |       |        | pet       |
      |   | categories |       |        | [monster] |
      | * | name       | web   | en_GB  | The Ziggy |
      | * | name       | web   | de_DE  | Die Ziggy |
      | * | name       | web   | ua_UA  | Зіггі     |

  Scenario: Transform scalar values with a series of "set" actions using expressions:
  - change family (concatenate with a prefix)
  - uppercase name in en_GB
  - add url_slug

    Given a product in the PIM:
      |   | field      | scope | locale | value            |
      |   | identifier |       |        | ziggy            |
      |   | family     |       |        | hydra            |
      |   | categories |       |        | [monster]        |
      | * | name       | web   | en_GB  | The Ziggy        |
      | * | name       | web   | de_DE  | Dēr Süße $Zïggy$ |

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
    Then the product in the PIM should look like:
      |   | field      | scope | locale | value            |
      |   | identifier |       |        | ziggy            |
      |   | family     |       |        | hydra_new        |
      |   | categories |       |        | [monster]        |
      | * | name       | web   | en_GB  | THE ZIGGY        |
      | * | name       | web   | de_DE  | Dēr Süße $Zïggy$ |
      | * | url_slug   |       | de_DE  | der-susse-ziggy  |
