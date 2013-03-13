<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/*
 * WordPress stubbornly will not load except from the global scope.
 */
if (strpos(realpath(__FILE__), 'wp-content' . DIRECTORY_SEPARATOR . 'plugins') !== false) {

    include substr(__FILE__, 0, strpos(__FILE__, 'wp-content/plugins/')) . 'wp-blog-header.php';
}

/**
 * Boot tubepress.
 */
require 'boot.php';

/**
 * Hand off the request to the Ajax handler.
 */
tubepress_impl_patterns_sl_ServiceLocator::getAjaxHandler()->handle();
