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
     * @var string
     */
    private $_templatePath;

    /**
     * @var tubepress_spi_options_ui_PluggableOptionsPageTab[]
     */
    private $_optionsTabs = array();

    public function __construct($templatePath)
    {
        $this->_templatePath = $templatePath;
    }

    /**
     * Generates the HTML for the "meat" of the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        $templateBuilder = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $template        = $templateBuilder->getNewTemplateInstance($this->_templatePath);
        $tabs            = $this->getDelegateFormHandlers();

        $template->setVariable(self::TEMPLATE_VAR_TABS, $tabs);

        $templateEvent = new tubepress_spi_event_EventBase($template);
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_TABS_ALL, $templateEvent);

        $template = $templateEvent->getSubject();

        return $template->toString();
    }

    /**
     * Allows this form handler to be uniquely identified.
     *
     * @return string All lowercase alphanumerics.
     */
    public function getName()
    {
        return 'tubepress_impl_options_ui_DefaultTabsHandler';
    }

    public function setPluggableOptionsPageTabs(array $tabs)
    {
        $this->_optionsTabs = $tabs;
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    protected final function getDelegateFormHandlers()
    {
        return $this->_optionsTabs;
    }
}