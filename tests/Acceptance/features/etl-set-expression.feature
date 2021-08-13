Feature: Data transformations using `set` actions and specific expressions
  As a user
  I want to change values of properties and attributes in Akeneo using expression language

  Scenario: Remove html tags from text and text area attribute values

    Given a product in the PIM with properties:
      | field      | value     |
      | identifier | ziggy     |
    And with a text attribute description:
      """
<br/>
<div class="lorem-ipsum">
    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
    <ol>
      <li>Aliquam tincidunt mauris eu risus.</li>
      <li>Vestibulum <span style="font-color: #AB1234">auctor dapibus</span> neque.</li>
    </ol>
</div>
      """

    And I apply transformations using the profile:
      """
      transform:
          actions:
              -
                  type: set
                  field: description
                  scope: null
                  expression: "removeHtmlTags(value('description', null, null))"
      """

    When transformation is executed
    Then the product in the PIM should have properties:
      | field      | value     |
      | identifier | ziggy     |
    And should have the text attribute description:
      """
Lorem ipsum dolor sit amet, consectetuer adipiscing elit.

Aliquam tincidunt mauris eu risus.
Vestibulum auctor dapibus neque.
      """
