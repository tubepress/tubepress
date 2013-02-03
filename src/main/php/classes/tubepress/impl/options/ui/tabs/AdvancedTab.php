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
 * Displays the advanced options tab.
 */
class tubepress_impl_options_ui_tabs_AdvancedTab extends tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab
{
    const TAB_NAME = 'advanced';

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected final function getRawTitle()
    {
        return 'Advanced';  //>(translatable)<
    }

    public final function getName()
    {
        return self::TAB_NAME;
    }
}