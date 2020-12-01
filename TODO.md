# Todo's

## Development

* Remove language from login url of facebook and google.
* Move files into shared location.
* Try updating to Drupal 9.1 normally out next Wednesday. Need composer 2.x as well for this.
* Create cleanup module to remove "Cron run completed." messages from the dblog:
 * A module with it's own cronjob that every so often through ultimate_cron will
   fetch all these messages, delete them and place a counter message in the
   watchdog table. This way we can see the cron runs every minute but doesn't
   create pages and pages of messages.
* Fix bug where appointment is one hour off on calendar (only on local?).
* Provide the first real behat tests.
* Fix profile picture!!!!!!
* Start cleaning up user sidebar.
* Contact page form.
* Fix breadcrumb.
* Change appointment creation message to be more user friendly.
* Create licensing options
* Calculate percentage booked to show on month calendar on days?
* See if we need to provide an ical to provide a feed for users appointments:
https://www.drupal.org/project/views_ical
* Whenever you need statistics look at using https://www.drupal.org/project/matomo

# Deployment

* Mount acme.json to avoid down-up certificate generation on prod server.
* Move deploy command from make into bash script in lib/scripts?
* Make sure the deployed files have the correct permissions (files folder)
* Move files into a shared location for prod, pre-prod and post prod?
* Initial setup seems to require to not have htaccess pass before lets-encrypt
  can request it's certificates.
* Files folder should be owned by user www-data so apache can write to it.

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
