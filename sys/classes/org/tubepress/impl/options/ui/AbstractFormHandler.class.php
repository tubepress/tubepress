<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_CategoryName',
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_template_Template',
    'org_tubepress_spi_options_ui_FormHandler',
    'org_tubepress_spi_options_ui_TabsHtmlGenerator',
    'org_tubepress_spi_options_ui_FilterHtmlGenerator',
));

/**
 * Displays a generic options form for TubePress
 *
 */
abstract class org_tubepress_impl_options_ui_AbstractFormHandler implements org_tubepress_spi_options_ui_FormHandler
{
    const TEMPLATE_VAR_TITLE     = 'optionsPageTitle';
    const TEMPLATE_VAR_INTRO     = 'optionsPageIntro';
    const TEMPLATE_VAR_TABS      = 'optionsPageTabs';
    const TEMPLATE_VAR_SAVE_TEXT = 'optionsPageSaveText';
    const TEMPLATE_VAR_SAVE_ID   = 'optionsPageSaveId';
    const TEMPLATE_VAR_FILTER    = 'optionsPageFilter';

    /**
     * Displays all the TubePress options in HTML
     *
     * @return string The HTML for the options page.
     */
    public function getHtml()
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        $templateBldr   = $ioc->get('org_tubepress_api_template_TemplateBuilder');
        $fse            = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $tabs           = $ioc->get('org_tubepress_spi_options_ui_TabsHtmlGenerator');
        $filter         = $ioc->get('org_tubepress_spi_options_ui_FilterHtmlGenerator');
        $basePath       = $fse->getTubePressBaseInstallationPath();
        $template       = $templateBldr->getNewTemplateInstance($basePath . '/' . $this->getRelativeTemplatePath());

        $template->setVariable(self::TEMPLATE_VAR_TITLE, $messageService->_('TubePress Options'));
        $template->setVariable(self::TEMPLATE_VAR_INTRO, $messageService->_('Set default options for the plugin. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more info. An asterisk (*) next to an option indicates it\'s only available with <a href="http://tubepress.org/features">TubePress Pro</a>.'));
        $template->setVariable(self::TEMPLATE_VAR_SAVE_TEXT, $messageService->_('Save'));
        $template->setVariable(self::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->setVariable(self::TEMPLATE_VAR_TABS, $tabs->getHtml());
        $template->setVariable(self::TEMPLATE_VAR_FILTER, $filter->getHtml());

        return $template->toString();
    }

    /**
    * Updates options from a keyed array
    *
    * @param array $postVars The POST variables
    *
    * @return unknown Null if there was no problem handling the submission, otherwise a string failure message.
    */
    public function onSubmit($postVars)
    {
        $ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
        $storageManager = $ioc->get('org_tubepress_api_options_StorageManager');
        $odr            = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $validator      = $ioc->get(org_tubepress_api_options_OptionValidator::_);
        $allOds         = $odr->findAll();

        /* this loop will collect everything except checkboxes */
        foreach ($postVars as $name => $value) {

            $descriptor = $odr->findOneByName($name);

            /* is this actually an option? */
            if ($descriptor === null) {

                continue;
            }

            /* we'll handle these later. */
            if ($descriptor->isBoolean()) {

                continue;
            }

            /* run it through validation. */
            if (! $validator->isValid($name, $value)) {

                return $validator->getFailureMessage($name, $value);
            }

            $storageManager->set($name, $value);
        }

        /* handle the booleans */
        foreach ($allOds as $descriptor) {

            /* ignore non-bools */
            if (! $descriptor->isBoolean()) {

                continue;
            }

            /* if the user checked the box, the option name will appear in the POST vars */
            $storageManager->set($name, array_key_exists($descriptor->getName(), $postVars));
        }

        return null;
    }

    protected abstract function getRelativeTemplatePath();
}
