Feature: Data transformations using `add` actions
  As a user
  I want to add items to values of properties and attributes

  Scenario: Add items to categories:
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
    Then the upload result should have properties:
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
    Then the upload result should have properties:
      | field      | value                         |
      | identifier | ziggy                         |
      | categories | [pim,pet,pxm,ziggy-the-hydra] |


  Scenario: Add items to associations
    Given a product in the PIM with properties:
      | field      | value |
      | identifier | ziggy |
    And associations:
      | type      | products | product_models | groups |
      | FRIENDS   | [fuzzy]  | []             | []     |
      | RELATIVES | []       | [izzy]         | []     |
      | OLD       | []       | [oldry]        | []     |

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
    Then the upload result should have properties:
      | field      | value |
      | identifier | ziggy |
    And the upload result should have associations:
      | type      | products            | product_models         | groups              |
      | FRIENDS   | [fuzzy,gizzy,jazzy] | []                     | []                  |
      | RELATIVES | []                  | [izzy,unicorn,mermaid] | [magical_creatures] |
      | OLD       | []                  | [oldry]                | []                  |
      | NEW       | []                  | []                     | [magical_creatures] |

  Scenario: Ensure that adding invalid items to associations
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
              type: add
              field: associations
              items:
                  FRIENDS:
                      products: 'jazzy'

      """
    When transformation is executed
    Then the upload result is empty

  Scenario: Add attributes to families
    Given a family in the PIM with properties:
      | field      | value                |
      | code       | ziggy-family         |
      | attributes | [sku,name,description] |

    And I apply transformations using the profile:
      """
      actions:
          -
              type: add
              field: attributes
              items: ['colour']
      """
    When transformation is executed
    Then the upload result should have properties:
      | field      | value                       |
      | code       | ziggy-family                |
      | attributes | [sku,name,description,colour] |
