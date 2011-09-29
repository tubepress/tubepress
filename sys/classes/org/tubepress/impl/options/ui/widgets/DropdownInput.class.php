<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_options_ui_widgets_AbstractWidget',
    'org_tubepress_impl_util_LangUtils',
));

/**
 * Displays a drop-down input.
 */
class org_tubepress_impl_options_ui_widgets_DropdownInput extends org_tubepress_impl_options_ui_widgets_AbstractWidget
{
    const _ = 'org_tubepress_impl_options_ui_widgets_DropdownInput';

    const TEMPLATE_VAR_ACCEPTABLE_VALUES = 'org_tubepress_impl_options_ui_widgets_DropdownInput__options';

    protected function getTemplatePath()
    {
        return 'sys/ui/templates/options_page/widgets/dropdown.tpl.php';
    }

    protected function populateTemplate($template, $currentValue)
    {
        $values = array();
        $map    = $this->getOptionDescriptor()->getAcceptableValues();

        if (! org_tubepress_impl_util_LangUtils::isAssociativeArray($map)) {

            throw new Exception(sprintf('"%s" has a non-associative array set for its value map', $this->getOptionDescriptor()->getName()));
        }

        foreach ($map as $key => $value) {

            $values[$this->getMessageService()->_($key)] = $value;
        }

        $template->setVariable(self::TEMPLATE_VAR_ACCEPTABLE_VALUES, $values);
    }
}