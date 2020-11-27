<?php

declare(strict_types=1);

namespace Appointment\Tests\Behat;

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Testwork\Tester\Result\TestResult;
use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines step definitions that are generally useful for the project.
 */
class FeatureContext extends RawDrupalContext {

  /**
   * Explicitly take a screenshot.
   *
   * @Given I take a screenshot
   * @Given I take a screenshot with the title :title
   */
  public function takeScreenshot($title = 'screenshot') {
    static $screenshot_count = 0;
    $driver = $this->getSession()->getDriver();

    // Get the screenshot if the driver supports it.
    try {
      $image = $driver->getScreenshot();
    }
    catch (UnsupportedDriverActionException $e) {
      return;
    }

    // Set default title.
    $title = sprintf(
      '%s_%s_%s',
      date("Ymd-Hi"),
      preg_replace('/[^a-zA-Z0-9\._-]/', '_', $title),
      (++$screenshot_count)
    );

    // Save the file locally, if a path is available. Variable can be set in
    // .travis.yml or in local working environment.
    // getenv('PANOPOLY_BEHAT_SCREENSHOT_PATH');.
    $local_screenshot_path = '/home/project';
    if (empty($local_screenshot_path)) {
      print "Environment variable PANOPOLY_BEHAT_SCREENSHOT_PATH is not set, unable to save screenshot\n";
    }
    elseif (!is_dir($local_screenshot_path)) {
      print "Directory $local_screenshot_path does not exist, unable to save screenshot\n";
    }
    else {
      $file_location = "$local_screenshot_path/$title.png";
      if (@file_put_contents($file_location, $image) !== FALSE) {
        print "Screenshot saved to $file_location\n";
      }
      else {
        print "Unable to save screenshot\n";
      }
    }
  }

  /**
   * After a failed step, upload a screenshot.
   *
   * @AfterStep
   */
  public function afterStepTakeScreenshot($event) {
    if ($event->getTestResult()->getResultCode() === TestResult::FAILED) {
      $this->takeScreenshot($event->getStep()->getText());
    }
  }

}
