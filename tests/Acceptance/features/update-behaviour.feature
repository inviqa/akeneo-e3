Feature: Ensuring compliance with Akeneo Update Behaviour
  As a developer
  I want to be sure that E3 fully complies with Akeneo Update Behaviour
  @see https://api.akeneo.com/documentation/update.html#update-behavior

  PATCH request updates only the specified keys according to the following rules:
  - Rule 1: If the value is an object, it will be merged with the old value.
  - Rule 2: If the value is not an object, it will replace the old value.
  - Rule 3: For non-scalar values (objects and arrays) data types must match.
  - Rule 4: Any data in non specified properties will be left untouched.

  Scenario: Rule 1: object update
    If the value is an object, it will be merged with the old value.

    Given an original resource:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """
    When I apply the patch request body
      """
      {
        "labels": {
          "de_DE": "Stiefel"
        }
      }
      """
    Then the resulting resource should be:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes",
          "de_DE": "Stiefel"
        }
      }
      """

  Scenario: Rule 2: non object update (first example)
    If the value is not an object, it will replace the old value.

    Given an original resource:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
    """
    When I apply the patch request body
      """
      {
        "parent": "clothes"
      }
      """
    Then the resulting resource should be:
      """
      {
        "code": "boots",
        "parent": "clothes",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """

  Scenario: Rule 2: non object update (second example)

    Given an original resource:
      """
      {
        "identifier": "boots-4846",
        "categories": ["shoes", "boots"]
      }
      """
    When I apply the patch request body
      """
      {
        "categories": ["boots"]
      }
      """
    Then the resulting resource should be:
      """
      {
        "identifier": "boots-4846",
        "categories": ["boots"]
      }
      """

  Scenario: Rule 3: validation on data types
    For non-scalar values (objects and arrays) data types must match.
    If they don't match, the resource will not be modified and
    Akeneo REST API should return a 422 error.

    Given an original resource:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """
    When I apply the patch request body
      """
      {
        "labels": null
      }
      """
    Then the resulting resource should be:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """


  Scenario: Rule 4: non specified properties
    Any data in non specified properties will be left untouched.

    Given an original resource:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """
    When I apply the patch request body
      """
      {
      }
      """
    Then the resulting resource should be:
      """
      {
        "code": "boots",
        "parent": "master",
        "labels": {
          "en_US": "Boots",
          "fr_FR": "Bottes"
        }
      }
      """

  Scenario: Update product values / Add a product value

    Given an original resource:
      """
      {
        "identifier": "boots-4846",
        "values": {
          "name": [
            {
              "locale": "en_US",
              "scope": null,
              "data": "Mug"
            }
          ]
        }
      }
      """
    When I apply the patch request body
      """
{
  "values": {
    "short_description": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "This mug is a must-have!"
      }
    ]
  }
}

      """
    Then the resulting resource should be:
      """
{
  "identifier": "boots-4846",
  "values": {
    "name": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "Mug"
      }
    ],
    "short_description": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "This mug is a must-have!"
      }
    ]
  }
}

      """

  Scenario: Update product values / Modify value that is already set

    Given an original resource:
      """
{
  "identifier": "boots-4846",
  "values": {
    "name": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "Incredible mug"
      },
      {
        "locale": "fr_FR",
        "scope": null,
        "data": "Tasse"
      }
    ],
    "short_description": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "This mug is a must-have!"
      }
    ]
  }
}
      """
    When I apply the patch request body
      """
{
  "values": {
    "name": [
      {
        "locale": "fr_FR",
        "scope": null,
        "data": "Tasse extraordinaire"
      }
    ]
  }
}
      """
    Then the resulting resource should be:
      """
{
  "identifier": "boots-4846",
  "values": {
    "name": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "Incredible mug"
      },
      {
        "locale": "fr_FR",
        "scope": null,
        "data": "Tasse extraordinaire"
      }
    ],
    "short_description": [
      {
        "locale": "en_US",
        "scope": null,
        "data": "This mug is a must-have!"
      }
    ]
  }
}
      """

