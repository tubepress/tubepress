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
 * Hidden field.
 */
class tubepress_core_options_ui_impl_fields_provided_HiddenField extends tubepress_core_options_ui_impl_fields_provided_AbstractProvidedOptionBasedField
{
    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/core/options-ui/resources/field-templates/hidden.tpl.php';
    }
}