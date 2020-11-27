Feature: Default
  Check if the site is functional.

  @api
  Scenario: Visit frontpages
    Given I am not logged in
    When I visit '/en'
    When I visit '/fr'
    When I visit '/nl'


