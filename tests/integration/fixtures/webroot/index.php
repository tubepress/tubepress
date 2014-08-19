<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

define('TUBEPRESS_CONTENT_DIRECTORY', realpath(__DIR__ . '/tubepress-content-directory'));

/** @noinspection PhpIncludeInspection */
$container = require __DIR__ . '/tubepress/src/platform/scripts/boot.php';

if (isset($_GET['options'])) {

    $options = $_GET['options'];

} else {

    $options = array();
}

/**
 * @var $context tubepress_app_api_options_ContextInterface
 */
$context = $container->get(tubepress_app_api_options_ContextInterface::_);

foreach ($options as $name => $val) {
    $context->setEphemeralOption($name, $val);
}

/**
 * @var $html tubepress_app_api_html_HtmlGeneratorInterface
 */
$html = $container->get(tubepress_app_api_html_HtmlGeneratorInterface::_);

/**
 * @var $env tubepress_app_api_environment_EnvironmentInterface
 */
$env = $container->get(tubepress_app_api_environment_EnvironmentInterface::_);
$env->setBaseUrl('http://localhost:54321/tubepress');
$footer = $html->getJS();
$header = $html->getCSS();
$content = $html->getHtml();

print <<<TOY
<!doctype html>

<html lang="en">
    <head>
      <meta charset="utf-8">
      <title>TubePress</title>
      $header
    </head>

    <body>
        $content
        $footer
    </body>
</html>
TOY;
