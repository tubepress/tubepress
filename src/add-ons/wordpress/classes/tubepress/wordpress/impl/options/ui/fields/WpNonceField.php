<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_wordpress_impl_options_ui_fields_WpNonceField implements tubepress_app_api_options_ui_FieldInterface
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_properties  = new tubepress_platform_impl_collection_Map();
    }

    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     *
     * @api
     * @since 4.0.0
     */
    public function getUntranslatedDescription()
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
        return $this->_wpFunctions->wp_nonce_field('tubepress-save', 'tubepress_nonce', true, false);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $this->_wpFunctions->check_admin_referer('tubepress-save', 'tubepress_nonce');

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
    public function getUntranslatedDisplayName()
    {
        return '';
    }

    /**
     * @return string The page-unique identifier for this item.
     */
    public function getId()
    {
        return 'tubepress_nonce';
    }

    /**
     * @return tubepress_platform_api_collection_MapInterface
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function setProperty($name, $value)
    {
        $this->_properties->put($name, $value);
    }
}