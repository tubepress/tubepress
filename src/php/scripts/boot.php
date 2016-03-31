<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

if (!class_exists('tubepress_internal_boot_InitialBootstrapper', false)) {

    require __DIR__ . '/../classes/internal/tubepress/internal/boot/InitialBootstrapper.php';
}

return tubepress_internal_boot_InitialBootstrapper::getServiceContainer();