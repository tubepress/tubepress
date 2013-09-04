<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_impl_options_ui_fields_GallerySourceField extends tubepress_impl_options_ui_fields_AbstractOptionsPageField
{

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getAbsolutePathToTemplate()
    {
        return '';
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        return array();
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $hrps      = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $fieldName = tubepress_api_const_options_names_Output::GALLERY_SOURCE;

        if (!$hrps->hasParam($fieldName)) {

            return null;
        }

        return $this->sendToStorage($fieldName, $hrps->getParamValue($fieldName));
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