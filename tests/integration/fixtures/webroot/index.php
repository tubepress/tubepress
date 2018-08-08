<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

ini_set('display_errors', 1);

define('TUBEPRESS_CONTENT_DIRECTORY', realpath(__DIR__ . '/tubepress-content-directory'));

/** @noinspection PhpIncludeInspection */
$container = require __DIR__ . '/tubepress/src/php/scripts/boot.php';

if (isset($_GET['options'])) {

    $options = unserialize(base64_decode($_GET['options']));

} else {

    $options = array();
}

/**
 * @var $context tubepress_api_options_ContextInterface
 */
$context = $container->get(tubepress_api_options_ContextInterface::_);

$context->setEphemeralOptions($options);

/**
 * @var $html tubepress_api_html_HtmlGeneratorInterface
 */
$html    = $container->get(tubepress_api_html_HtmlGeneratorInterface::_);
$footer  = $html->getJS();
$header  = $html->getCSS();
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
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        $footer
    </body>
</html>
TOY;
