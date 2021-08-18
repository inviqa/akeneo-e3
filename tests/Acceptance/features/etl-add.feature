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
