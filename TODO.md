# Todo's

## Development

* Getting an error: Warning: touch(): Utime failed: Operation not permitted in /home/project/web/core/lib/Drupal/Component/PhpStorage/MTimeProtectedFastFileStorage.php on line 100
 * https://www.drupal.org/project/drupal/issues/2712769
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

# Deployment

* Make sure the deployed files have the correct permissions (files folder)
* Create command to download release and place files in pre-production.

# Behat testing

* Anonymous testing
 * Visit required pages
 * Access control on non accessible pages
 * Filter the search view
* Authenticated testing
 * Log in
 * Create appointment
 * Log out
