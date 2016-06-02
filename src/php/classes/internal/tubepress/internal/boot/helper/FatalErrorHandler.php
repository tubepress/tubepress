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

class tubepress_internal_boot_helper_FatalErrorHandler
{
    public function onFatalError(tubepress_api_log_LoggerInterface $logger, array $error)
    {
        try {

            if (!$this->_isErrorFatal($error)) {

                return;
            }

            $logger->error(sprintf(
                'Fatal error (type <code>%s</code>) detected on line <code>%s</code> of <code>%s</code>: <code>%s</code>',
                $error['type'],
                $error['line'],
                htmlspecialchars($error['file']),
                htmlspecialchars($error['message'])
            ));

        } catch (\Exception $e) {

            //we tried
        }
    }

    private function _isErrorFatal(array $error)
    {
        $level   = $error['type'];
        $errors  = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_COMPILE_ERROR;

        return ($level & $errors) > 0;
    }
}