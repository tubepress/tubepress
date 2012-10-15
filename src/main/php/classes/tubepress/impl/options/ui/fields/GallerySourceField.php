<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * A field suited for display on the gallery source tab.
 */
class tubepress_impl_options_ui_fields_GallerySourceField implements tubepress_spi_options_ui_PluggableOptionsPageField
{
    private $_name;

    private $_field;

    public function  __construct($gallerySourceName, tubepress_spi_options_ui_PluggableOptionsPageField $field)
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

    /**
     * Gets the providers to which this field applies.
     *
     * @return array An array of provider names to which this field applies. May be empty. Never null.
     */
    public final function getArrayOfApplicableProviderNames()
    {
        return $this->_field->getArrayOfApplicableProviderNames();
    }

    public final function getDesiredTabName()
    {
        return $this->_field->getDesiredTabName();
    }

    public final function getGallerySourceName()
    {
        return $this->_name;
    }
}