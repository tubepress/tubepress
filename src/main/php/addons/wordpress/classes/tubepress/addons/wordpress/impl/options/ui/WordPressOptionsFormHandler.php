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
 * Displays a WordPress-specific options form for TubePress.
 */
class tubepress_addons_wordpress_impl_options_ui_WordPressOptionsFormHandler extends tubepress_impl_options_ui_AbstractFormHandler
{
    protected final function getRelativeTemplatePath()
    {
        return 'src/main/php/addons/wordpress/resources/templates/options_page.tpl.php';
    }
}
