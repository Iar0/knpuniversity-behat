Feature: Authentication
  In order to gain access to the site management area
  As an admin
  I need to be able to login and logout


  Scenario: Loggin in
    Given there is an admin user with name "admin" and password "admin"
    And I am on "/"
    When I follow "Login"
    And I fill in "Username" with "admin"
    And I fill in "Password" with "admin"
    And I press "Login"
    Then I should see "Logout"