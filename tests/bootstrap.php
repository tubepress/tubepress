<?php

// We define the COMPOSER_INSTALL constant, so that PHPUnit knows where to
// autoload from. This is needed for tests run in isolation mode.
// https://www.drupal.org/node/2597814
if (!defined('PHPUNIT_COMPOSER_INSTALL')) {

  define('PHPUNIT_COMPOSER_INSTALL', __DIR__ . '/../vendor/autoload.php');
}

return require __DIR__ . '/../vendor/autoload.php';