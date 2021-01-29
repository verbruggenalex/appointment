# Todo's

## Development

* Buy and set the real logo!!!! :) Trust in God.
* Provide translations for user sidebar.
* Provide the first real behat tests.
* Start cleaning up user sidebar.
* Contact page form.
* Fix breadcrumb.
* Remove language from login url of facebook and google.
* Change appointment creation message to be more user friendly.
* Create licensing options
* Calculate percentage booked to show on month calendar on days?
* See if we need to provide an ical to provide a feed for users appointments:
https://www.drupal.org/project/views_ical
* Whenever you need statistics look at using https://www.drupal.org/project/matomo

# Deployment

* Start working with pull requests.
* Think about the deployment if we really want to use the Makefile?
* Mount acme.json to avoid down-up certificate generation on prod server.
* Move deploy command from make into bash script in lib/scripts?
* Make sure the deployed files have the correct permissions (files folder)
* Initial setup seems to require to not have htaccess pass before lets-encrypt
  can request it's certificates.

# Behat testing

* Anonymous testing
 * Visit required pages
 * Access control on non accessible pages
 * Filter the search view
* Authenticated testing
 * Log in
 * Create appointment
 * Log out

# Theia image:

* add twig2 extension
