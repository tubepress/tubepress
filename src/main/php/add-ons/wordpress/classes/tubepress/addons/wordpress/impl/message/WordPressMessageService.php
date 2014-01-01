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
 * Message service that uses gettext (via WordPress).
 */
class tubepress_addons_wordpress_impl_message_WordPressMessageService implements tubepress_spi_message_MessageService
{
    /**
     * Retrieves a message for TubePress
     *
     * @param string $message The message ID
     *
     * @return string The corresponding message, or "" if not found
     */
    public final function _($message)
    {
        $wordPressFunctionWrapperService = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        return $message == '' ? '' : $wordPressFunctionWrapperService->__($message, 'tubepress');
    }
}
