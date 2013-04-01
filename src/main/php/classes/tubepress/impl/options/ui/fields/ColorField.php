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
 * Displays a color-chooser input.
 */
class tubepress_impl_options_ui_fields_ColorField extends tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField
{
    const FIELD_CLASS_NAME = 'tubepress_impl_options_ui_fields_ColorField';

    /**
     * Get the path to the template for this field, relative
     * to TubePress's root.
     *
     * @return string The path to the template for this field, relative
     *                to TubePress's root.
     */
    protected final function getTemplatePath()
    {
        return 'src/main/resources/system-templates/options_page/fields/color.tpl.php';
    }
}