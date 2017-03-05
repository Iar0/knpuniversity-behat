Feature: Search
  In order to find products dinosaurs love
  As a web user
  I need to be able to search for products

  Background:
    Given I am on "/"

  Scenario Outline: Search for a product
#    When I fill in "searchTerm" with "<term>"
#    And I press "search_submit"
    When I fill in the search box with "<term>"
    And I press the search button
    Then I should see "<result>"
    Examples:
      | term    | result              |
      | Samsung | Samsung Galaxy S II |
      | xBox    | No products found   |

