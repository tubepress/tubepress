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
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_impl_options_ui_fields_ThemeField extends tubepress_impl_options_ui_fields_DropdownField
{
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
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        $defaultThemesPath = $environmentDetector->getTubePressBaseInstallationPath() . '/src/main/resources/default-themes';
        $userThemesPath    = $environmentDetector->getUserContentDirectory() . '/themes';

        return sprintf($originalDescription, $userThemesPath, $defaultThemesPath);
    }
}