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
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_addons_core_impl_options_ui_fields_ThemeField extends tubepress_impl_options_ui_fields_DropdownField
{
    public function __construct()
    {
        parent::__construct(tubepress_api_const_options_names_Thumbs::THEME);
    }

    /**
     * Override point.
     *
     * Allows subclasses to further modify the description for this field.
     *
     * @param $originalDescription string The original description as calculated by AbstractField.php.
     *
     * @return string The (possibly) modified description for this field.
     */
    protected final function getModifiedDescription($originalDescription)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        $defaultThemesPath = TUBEPRESS_ROOT . '/src/main/resources/default-themes';
        $userThemesPath    = $environmentDetector->getUserContentDirectory() . '/themes';

        return sprintf($originalDescription, $userThemesPath, $defaultThemesPath);
    }
}