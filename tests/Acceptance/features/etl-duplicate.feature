Feature: Data transformations in `duplicate` mode
  As a user
  I want to duplicate data from Akeneo

  Scenario: Duplicate a resource with all its properties and values using no actions

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
    And associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | []       | [izzy]         | []     |

    And I apply transformations using the profile:
      """
      actions: []
      upload-mode: duplicate
      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     | hydra     |
      | parent     | akeneo    |
      | categories | [monster] |
    And should have attributes:
      | attribute | scope | locale | value     |
      | name      | web   | en_GB  | The Ziggy |
      | name      | web   | de_DE  | Der Ziggy |
    And should have associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | []       | [izzy]         | []     |


  Scenario: Duplicate a resource and apply actions
  - set family to null
  - change parent
  - add name localisation
  - change association

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
    And associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | []       | [izzy]         | []     |

    And I apply transformations using the profile:
      """
      actions:
          -
              type: set
              field: family
              value: null
          -
              type: set
              field: parent
              value: me
          -
              type: set
              field: name
              scope: web
              locale: uk_UA
              value: Зіггі
          -
              type: set
              field: associations
              value:
                  RELATIVES:
                      groups: ['magical_creatures']
      upload-mode: duplicate
      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
      | family     |           |
      | parent     | me        |
      | categories | [monster] |
    And should have attributes:
      | attribute | scope | locale | value     |
      | name      | web   | en_GB  | The Ziggy |
      | name      | web   | de_DE  | Der Ziggy |
      | name      | web   | uk_UA  | Зіггі     |
    And should have associations:
      | type      | products | product_models | groups              |
      | FRIENDS   | [fuzzy]  | []             | []                  |
      | RELATIVES | []       | [izzy]         | [magical_creatures] |
