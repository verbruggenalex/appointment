# Todo's

## Development

* Trusted host settings for local, ci and production.
* Apcu to prod image?
* Setup cronjobs on image and in Drupal (each minute, ultimate_cron?)
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

# Behat testing

* Anonymous testing
 * Visit required pages
 * Access control on non accessible pages
 * Filter the search view
* Authenticated testing
 * Log in
 * Create appointment
 * Log out
