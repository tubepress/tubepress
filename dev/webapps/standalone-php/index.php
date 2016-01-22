<!doctype html>

<?php

if (!defined('TUBEPRESS_CONTENT_DIRECTORY')) {
    define('TUBEPRESS_CONTENT_DIRECTORY', __DIR__ . '/tubepress-content');
}

$serviceContainer = require '/var/www/tubepress/src/php/scripts/boot.php';
$generator        = $serviceContainer->get(tubepress_api_html_HtmlGeneratorInterface::_);

?>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>TubePress Standalone PHP Test</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">

        <?php print $generator->getCSS(); ?>
    </head>
    <body>

        <?php print $generator->getHTML('mode="tag" tagValue="pittsburgh steelers" resultsPerPage="3"'); ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

        <?php print $generator->getJS(false); ?>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    </body>
</html>