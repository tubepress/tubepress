<?php
/**
Plugin Name: @TubePress@
Plugin URI: http://tubepress.com
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and/or sidebar. @description@
Author: TubePress LLC
Version: git-bleeding
Author URI: http://tubepress.com

Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)

This file is part of TubePress (http://tubepress.com)

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
*/

include 'src/main/php/scripts/boot.php';

require 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'http://snippets.wp.tubepress.com/update.php',
    __FILE__,
    plugin_basename(basename(dirname(__FILE__)) . '/tubepress.php')
);