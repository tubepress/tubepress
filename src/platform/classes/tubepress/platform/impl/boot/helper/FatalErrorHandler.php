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

class tubepress_platform_impl_boot_helper_FatalErrorHandler
{
    public function onFatalError()
    {
        $error = error_get_last();

        if (!is_array($error) || !isset($error['file'])) {

            return;
        }

        if (strpos($_SERVER['REQUEST_URI'], 'tubepress-tried-recovery') !== false) {

            return;
        }

        $needle     = 'tubepress-service-container.php';
        $fileName   = $error['file'];
        $fileLength = strlen($needle);
        $start      = $fileLength * -1;

        if (substr($fileName, $start) === $needle) {

            $unlinked = unlink($fileName);

            if ($unlinked !== true || !isset($_SERVER['REQUEST_URI'])) {

                return;
            }

            $uri = $_SERVER['REQUEST_URI'] . '&tubepress-tried-recovery';

            echo <<<YYY
<div>TubePress detected a fatal error with its system cache and is attempting to resolve the issue. Refreshing this page now...</div>
<script type="text/javascript">window.location.replace("$uri");</script>
YYY
            ;
        }
    }
}