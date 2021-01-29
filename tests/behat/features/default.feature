Feature: Default
  Check if the site is functional.

  @api @javascript
  Scenario: Visit frontpages
    Given I am not logged in
    When I visit '/en'
    When I visit '/fr'
    When I visit '/nl'
    When I visit '/en/business/barzan'
    When I visit '/fr/business/barzan'
    When I visit '/nl/business/barzan'


