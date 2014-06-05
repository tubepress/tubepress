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
class tubepress_wordpress_impl_options_ui_fields_WpNonceField implements tubepress_core_options_ui_api_FieldInterface
{
    /**
     * @var tubepress_wordpress_spi_WpFunctionsInterface
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_spi_WpFunctionsInterface $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     *
     * @api
     * @since 4.0.0
     */
    public function getTranslatedDescription()
    {
        return '';
    }

    /**
     * @return string The widget HTML for this form element.
     *
     * @api
     * @since 4.0.0
     */
    public function getWidgetHTML()
    {
        return $this->_wpFunctions->wp_nonce_field('tubepress-save', 'tubepress-nonce', true, false);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $this->_wpFunctions->check_admin_referer('tubepress-save', 'tubepress-nonce');

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

    /**
     * @return string The name of the item that is displayed to the user.
     */
    public function getTranslatedDisplayName()
    {
        return '';
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return 'tubepress-nonce';
    }
}