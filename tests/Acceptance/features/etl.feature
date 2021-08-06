Feature: ETL

  Scenario: Replace the family code

    Given a product in the PIM:
      | field      | value |
      | identifier | ziggy |
      | family     | hydra |

    And an ETL profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: family
                  value: pet

      """

    When transformation is executed
    Then the product in the PIM should look like:
      | field      | value |
      | identifier | ziggy |
      | family     | pet   |


  Scenario: UPPERCASE and prefix the family code

    Given a product in the PIM:
      | field      | value |
      | identifier | ziggy |
      | family     | hydra |

    And an ETL profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: family
                  expression: "uppercase(family)~'_NEW'"

      """

    When transformation is executed
    Then the product in the PIM should look like:
      | field      | value     |
      | identifier | ziggy     |
      | family     | HYDRA_NEW |
