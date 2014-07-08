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
 * Displays a checkbox input.
 */
class tubepress_app_options_ui_impl_fields_provided_BooleanField extends tubepress_app_options_ui_impl_fields_provided_AbstractProvidedOptionBasedField
{
    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/checkbox.tpl.php';
    }
}