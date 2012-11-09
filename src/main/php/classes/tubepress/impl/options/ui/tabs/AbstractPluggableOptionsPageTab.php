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
 * Displays a tab on the options page.
 */
abstract class tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab extends tubepress_impl_options_ui_AbstractDelegatingFormHandler implements tubepress_spi_options_ui_PluggableOptionsPageTab
{
    const TEMPLATE_VAR_PARTICIPANT_ARRAY = 'tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab__participantArray';
    const TEMPLATE_VAR_TAB_NAME          = 'tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab__tabName';

    /**
     * Get the title of this tab.
     *
     * @return string The title of this tab.
     */
    public final function getTitle()
    {
        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        return $messageService->_($this->getRawTitle());
    }

    /**
     * Generates the HTML for the options form.
     *
     * @return string The HTML for the options form.
     */
    public final function getHtml()
    {
        global $tubepress_base_url;

        $templateBuilder         = tubepress_impl_patterns_sl_ServiceLocator::getTemplateBuilder();
        $template                = $templateBuilder->getNewTemplateInstance(TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . $this->getTemplatePath());
        $optionsPageParticipants = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPageParticipants();
        $tabParticipants         = array();

        foreach ($optionsPageParticipants as $optionsPageParticipant) {

            if (count($optionsPageParticipant->getFieldsForTab($this->getName())) > 0) {

                array_push($tabParticipants, $optionsPageParticipant);
            }
        }

        $template->setVariable(self::TEMPLATE_VAR_PARTICIPANT_ARRAY, $tabParticipants);
        $template->setVariable(self::TEMPLATE_VAR_TAB_NAME, $this->getName());
        $template->setVariable(tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $tubepress_base_url);

        $this->addToTemplate($template);

        return $template->toString();
    }

    /**
     * Get the delegate form handlers.
     *
     * @return array An array of tubepress_spi_options_ui_FormHandler.
     */
    public final function getDelegateFormHandlers()
    {
        $fields = array();

        $optionsPageParticipants = tubepress_impl_patterns_sl_ServiceLocator::getOptionsPageParticipants();

        foreach ($optionsPageParticipants as $optionsPageParticipant) {

            $fields = array_merge($fields, $optionsPageParticipant->getFieldsForTab($this->getName()));
        }

        return $fields;
    }

    /**
     * Get the untranslated title of this tab.
     *
     * @return string The untranslated title of this tab.
     */
    protected abstract function getRawTitle();

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to the template.
     *
     * @param ehough_contemplate_api_Template $template The template for this tab.
     */
    protected function addToTemplate(ehough_contemplate_api_Template $template)
    {
        //override point
    }

    /**
     * Override point.
     *
     * Allows subclasses to change the template path.
     *
     * @param $originaltemplatePath string The original template path.
     *
     * @return string The (possibly) modified template path.
     */
    protected function getModifiedTemplatePath($originaltemplatePath)
    {
        return $originaltemplatePath;
    }

    /**
     * Get the path to the template for this field, relative
     * to TubePress's root.
     *
     * @return string The path to the template for this field, relative
     *                to TubePress's root.
     */
    protected final function getTemplatePath()
    {
        $original = 'src/main/resources/system-templates/options_page/tab.tpl.php';

        return $this->getModifiedTemplatePath($original);
    }

}