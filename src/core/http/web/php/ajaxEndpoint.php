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

/*
 * WordPress stubbornly will not load except from the global scope.
 */
$__typicalWordPressPath = 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;

if (strpos(realpath(__FILE__), $__typicalWordPressPath) !== false) {

    /** @noinspection PhpIncludeInspection */
    include substr(__FILE__, 0, strpos(__FILE__, $__typicalWordPressPath)) . 'wp-blog-header.php';
}

/**
 * Boot tubepress.
 */
$serviceContainer = require '../../../../platform/scripts/boot.php';

/**
 * @var $ajaxHandler tubepress_core_http_api_AjaxCommandInterface
 */
$ajaxHandler = $serviceContainer->get(tubepress_core_http_api_AjaxCommandInterface::_);

$ajaxHandler->handle();