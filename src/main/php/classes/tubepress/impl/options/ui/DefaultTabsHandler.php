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
 * Generates the "meat" of the options form (in the form of tabs).
 */
class tubepress_impl_options_ui_DefaultTabsHandler extends tubepress_impl_options_ui_AbstractDelegatingFormHandler implements tubepress_spi_options_ui_FormHandler
{
    const TEMPLATE_VAR_TABS = 'tubepress_impl_options_ui_DefaultTabsHandler__tabs';

    /**
     * Generates the HTML for the "meat" of the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $template        = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php');
        $tabs            = $this->getDelegateFormHandlers();

        $template->setVariable(self::TEMPLATE_VAR_TABS, $tabs);

        return $template->toString();
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected final function getDelegateFormHandlers()
    {
        return tubepress_impl_patterns_sl_ServiceLocator::getOptionsPageTabs();
    }
}