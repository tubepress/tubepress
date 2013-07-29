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
 * Displays a generic options form for TubePress
 *
 */
class tubepress_impl_options_ui_DefaultFormHandler extends tubepress_impl_options_ui_AbstractDelegatingFormHandler
{
    const TEMPLATE_VAR_TITLE     = 'optionsPageTitle';
    const TEMPLATE_VAR_INTRO     = 'optionsPageIntro';
    const TEMPLATE_VAR_TABS      = 'optionsPageTabs';
    const TEMPLATE_VAR_SAVE_TEXT = 'optionsPageSaveText';
    const TEMPLATE_VAR_SAVE_ID   = 'optionsPageSaveId';
    const TEMPLATE_VAR_FILTER    = 'optionsPageFilter';

    /**
     * @var tubepress_spi_options_ui_FormHandler
     */
    private $_tabs;

    /**
     * @var tubepress_spi_options_ui_Field
     */
    private $_filterField;

    /**
     * @var string The path to the template for the form.
     */
    private $_templatePath;

    public function __construct(

        tubepress_spi_options_ui_FormHandler $tabs,
        tubepress_spi_options_ui_Field       $filterField,
        $templatePath)
    {
        $this->_tabs         = $tabs;
        $this->_filterField  = $filterField;
        $this->_templatePath = $templatePath;
    }

    /**
     * Displays all the TubePress options in HTML
     *
     * @return string The HTML for the options page.
     */
    public function getHtml()
    {
        $messageService  = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $templateBldr    = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $template        = $templateBldr->getNewTemplateInstance($this->_templatePath);

        $template->setVariable(self::TEMPLATE_VAR_SAVE_TEXT, $messageService->_('Save'));                                                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->setVariable(self::TEMPLATE_VAR_TABS, $this->_tabs->getHtml());
        $template->setVariable(self::TEMPLATE_VAR_FILTER, $this->_filterField);

        $templateEvent = new tubepress_spi_event_EventBase($template);
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN, $templateEvent);

        return $template->toString();
    }

    /**
     * Allows this form handler to be uniquely identified.
     *
     * @return string All lowercase alphanumerics.
     */
    public function getName()
    {
        return 'tubepress_impl_options_ui_DefaultFormHandler';
    }

    protected function getDelegateFormHandlers()
    {
        return array($this->_tabs, $this->_filterField);
    }
}