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
 * WP nonce field.
 */
class tubepress_addons_wordpress_impl_options_ui_fields_WpNonceField extends tubepress_impl_options_ui_OptionsPageItem implements tubepress_spi_options_ui_OptionsPageFieldInterface
{
    public function __construct()
    {
        parent::__construct('tubepress-nonce');
    }

    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     */
    public function getTranslatedDescription()
    {
        return '';
    }

    /**
     * @return string The widget HTML for this form element.
     */
    public function getWidgetHTML()
    {
        $wpFunctions = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        return $wpFunctions->wp_nonce_field('tubepress-save', 'tubepress-nonce', true, false);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $wpFunctions = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);

        $wpFunctions->check_admin_referer('tubepress-save', 'tubepress-nonce');

        return null;
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return false;
    }
}