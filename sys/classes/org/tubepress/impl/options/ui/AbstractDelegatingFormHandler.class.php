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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_options_ui_FormHandler'
));

/**
 * Handles nested submit handlers.
 */
abstract class org_tubepress_impl_options_ui_AbstractDelegatingFormHandler implements org_tubepress_api_options_ui_FormHandler
{
    /**
    * Updates options from a keyed array
    *
    * @return unknown Null if there was no problem handling the submission, otherwise an array
    * of string failure messages.
    */
    public function onSubmit()
    {
        $formHandlerInstances = $this->getDelegateFormHandlers();

        if (! is_array($formHandlerInstances)) {

            throw new Exception('Must pass an array of form handler instances');
        }


        $failures = array();

        foreach ($formHandlerInstances as $formHandlerInstance) {

            $result = $formHandlerInstance->onSubmit();

            if (is_array($result) && ! empty($result)) {

                $failures = array_merge($failures, $result);
            }
        }

        if (empty($failures)) {

            return null;
        }

        return $failures;
    }

    protected abstract function getDelegateFormHandlers();
}
