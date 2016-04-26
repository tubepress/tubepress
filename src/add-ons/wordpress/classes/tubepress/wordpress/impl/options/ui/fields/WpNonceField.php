<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_wordpress_impl_options_ui_fields_WpNonceField implements tubepress_api_options_ui_FieldInterface
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_properties;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_properties  = new tubepress_internal_collection_Map();
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDescription()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetHTML()
    {
        return $this->_wpFunctions->wp_nonce_field('tubepress-save', 'tubepress_nonce', true, false);
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        $this->_wpFunctions->check_admin_referer('tubepress-save', 'tubepress_nonce');

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedDisplayName()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'tubepress_nonce';
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($name, $value)
    {
        $this->_properties->put($name, $value);
    }
}
