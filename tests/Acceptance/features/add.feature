Feature: Data transformations using `add` actions
  As a user
  I want to add items to values of properties and attributes

  Scenario: Add fixed items to categories:
    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | categories | [pim,pet] |
    And I apply transformations using the profile:
      """
      actions:
          -
              type: add
              field: categories
              items:
                  - pxm
                  - fun
      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value             |
      | identifier | ziggy             |
      | categories | [pim,pet,pxm,fun] |

  Scenario: Add items generated using an expression to categories:
    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
      | categories | [pim,pet] |
    And I apply transformations using the profile:
      """
      actions:
          -
              type: add
              field: categories
              expression: '["pxm", identifier ~ "-the-hydra"]'
      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value                         |
      | identifier | ziggy                         |
      | categories | [pim,pet,pxm,ziggy-the-hydra] |


  Scenario: Add product associations

    Given a product in the PIM with properties:
      | field      | value |
      | identifier | ziggy |
    And associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | []       | [izzy]         | []     |

    And I apply transformations using the profile:
      """
      actions:
          -
              type: add
              field: associations
              items:
                  FRIENDS:
                      products: ['gizzy', 'jazzy']
                  RELATIVES:
                      product_models: ['unicorn', 'mermaid']
                      groups: ['magical_creatures']
                  NEW:
                      groups: ['magical_creatures']

      """
    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value |
      | identifier | ziggy |
    And should have associations:
      | type      | products            | product_models         | groups              |
      | FRIENDS   | [fuzzy,gizzy,jazzy] | []                     | []                  |
      | RELATIVES | []                  | [izzy,unicorn,mermaid] | [magical_creatures] |
      | NEW       | []                  | []                     | [magical_creatures] |
