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
class tubepress_wordpress_impl_message_WordPressMessageService implements tubepress_lib_translation_api_TranslatorInterface
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * Retrieves a message for TubePress
     *
     * @param string $message The message ID
     *
     * @return string The corresponding message, or "" if not found
     */
    public function _($message)
    {
        return $message == '' ? '' : $this->_wpFunctions->__($message, 'tubepress');
    }
}