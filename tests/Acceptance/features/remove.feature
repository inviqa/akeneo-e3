Feature: Data transformations using `remove` actions
  As a user
  I want to remove items to values of properties and attributes

  Scenario: Remove items from categories:
    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | categories | [pim,pet] |
    And I apply transformations using the profile:
      """
      actions:
          -
              type: remove
              field: categories
              items:
                  - pim
      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value |
      | identifier | ziggy |
      | categories | [pet] |

  Scenario: Remove items generated using an expression from categories:
    Given a product in the PIM with properties:
      | field      | value         |
      | identifier | ziggy         |
      | categories | [pim,pet,pxm] |
    And I apply transformations using the profile:
      """
      actions:
          -
              type: remove
              field: categories
              expression: '[lowerCase("PET")]'
      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
      | categories | [pim,pxm] |


  Scenario: Remove items from associations

    Given a product in the PIM with properties:
      | field      | value |
      | identifier | ziggy |
    And associations:
      | type      | products      | product_models | groups        |
      | FRIENDS   | [fuzzy,fizzy] | []             | [gozzi,guzzi] |
      | RELATIVES | []            | [izzy,chezzi]  | []            |

    And I apply transformations using the profile:
      """
      actions:
          -
              type: remove
              field: associations
              items:
                  FRIENDS:
                      products: ['fuzzy']
                      groups: ['guzzi']
                  RELATIVES:
                      product_models: ['izzy']
      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value |
      | identifier | ziggy |
    And should have associations:
      | type      | products | product_models | groups  |
      | FRIENDS   | [fizzy]  | []             | [gozzi] |
      | RELATIVES | []       | [chezzi]       | []      |

  Scenario: Ensure that removing invalid items from associations
  don't change data (Rule 3 of the Update Behavior)

    Given a product in the PIM with properties:
      | field      | value |
      | identifier | ziggy |
    And associations:
      | type    | products | product_models | groups |
      | FRIENDS | [fuzzy]  | []             | []     |

    And I apply transformations using the profile:
      """
      actions:
          -
              type: remove
              field: associations
              items:
                  FRIENDS:
                      products: 'jazzy'

      """
    When transformation is executed
    Then the product in the PIM is not modified
