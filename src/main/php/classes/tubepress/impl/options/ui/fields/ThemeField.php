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
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_impl_options_ui_fields_ThemeField extends tubepress_impl_options_ui_fields_DropdownField
{
    const FIELD_CLASS_NAME = 'tubepress_impl_options_ui_fields_ThemeField';

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