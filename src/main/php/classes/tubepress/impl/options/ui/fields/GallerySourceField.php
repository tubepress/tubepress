<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A field suited for display on the gallery source tab.
 */
class tubepress_impl_options_ui_fields_GallerySourceField implements tubepress_spi_options_ui_Field
{
    private $_name;

    private $_field;

    public function  __construct($gallerySourceName, tubepress_spi_options_ui_Field $field)
    {
        $this->_name  = $gallerySourceName;
        $this->_field = $field;
    }

    /**
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        return $this->_field->getHtml();
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public final function onSubmit()
    {
        return $this->_field->onSubmit();
    }

    /**
     * Gets the title of this field, usually consumed by humans.
     *
     * @return string The title of this field. May be empty or null.
     */
    public final function getTitle()
    {
        return $this->_field->getTitle();
    }

    /**
     * Gets the description of this field, usually consumed by humans.
     *
     * @return string The description of this field. May be empty or null.
     */
    public final function getDescription()
    {
        return $this->_field->getDescription();
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public final function isProOnly()
    {
        return $this->_field->isProOnly();
    }

    public final function getGallerySourceName()
    {
        return $this->_name;
    }
}