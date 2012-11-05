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
        $messageService = tubepress_impl_patterns_ioc_KernelServiceLocator::getMessageService();
        $templateBldr   = tubepress_impl_patterns_ioc_KernelServiceLocator::getTemplateBuilder();
        $template       = $templateBldr->getNewTemplateInstance(TUBEPRESS_ROOT . '/' . $this->getRelativeTemplatePath());

        $template->setVariable(self::TEMPLATE_VAR_TITLE, $messageService->_('TubePress Options'));                                                                                                                                                                                                                                                                                                                                 //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_INTRO, $messageService->_('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')); //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_TEXT, $messageService->_('Save'));                                                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $template->setVariable(self::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->setVariable(self::TEMPLATE_VAR_TABS, $this->_tabs->getHtml());
        $template->setVariable(self::TEMPLATE_VAR_FILTER, $this->_filterField);

        return $template->toString();
    }

    protected function getDelegateFormHandlers()
    {
        return array($this->_tabs, $this->_filterField);
    }

    protected abstract function getRelativeTemplatePath();
}