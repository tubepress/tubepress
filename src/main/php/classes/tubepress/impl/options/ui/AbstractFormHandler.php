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
abstract class tubepress_impl_options_ui_AbstractFormHandler extends tubepress_impl_options_ui_AbstractDelegatingFormHandler
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

    public function __construct(

        tubepress_spi_options_ui_FormHandler $tabs,
        tubepress_spi_options_ui_Field       $filterField)
    {
        $this->_tabs        = $tabs;
        $this->_filterField = $filterField;
    }


    /**
     * Displays all the TubePress options in HTML
     *
     * @return string The HTML for the options page.
     */
    public function getHtml()
    {
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $templateBldr   = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $template       = $templateBldr->getNewTemplateInstance(TUBEPRESS_ROOT . '/' . $this->getRelativeTemplatePath());

        $template->setVariable(self::TEMPLATE_VAR_TITLE, $messageService->_('TubePress Options'));                                                                                                                                                                                                                                                                                                                                 //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_INTRO, $messageService->_('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')); //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_TEXT, $messageService->_('Save'));                                                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->setVariable(self::TEMPLATE_VAR_TABS, $this->_tabs->getHtml());
        $template->setVariable(self::TEMPLATE_VAR_FILTER, $this->_filterField);

        $this->onPreTemplateToString($template);

        return $template->toString();
    }

    protected function getDelegateFormHandlers()
    {
        return array($this->_tabs, $this->_filterField);
    }

    protected abstract function getRelativeTemplatePath();

    //override point
    protected function onPreTemplateToString(ehough_contemplate_api_Template $template)
    {
        return;
    }
}