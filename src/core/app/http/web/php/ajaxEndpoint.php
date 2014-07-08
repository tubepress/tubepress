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
/**
 * Boot tubepress.
 */
$serviceContainer = require '../../../../../platform/scripts/boot.php';

/**
 * @var $ajaxHandler tubepress_app_http_api_AjaxCommandInterface
 */
$ajaxHandler = $serviceContainer->get(tubepress_app_http_api_AjaxCommandInterface::_);

$ajaxHandler->handle();