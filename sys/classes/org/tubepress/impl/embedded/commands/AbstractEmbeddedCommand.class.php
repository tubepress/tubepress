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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_patterns_cor_Command',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Base class for embedded commands.
 */
abstract class org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand implements org_tubepress_api_patterns_cor_Command
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {    
        global $tubepress_base_url;
       
        $providerName = $context->getProviderName();
        $videoId      = $context->getVideoId();
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom         = $ioc->get('org_tubepress_api_options_OptionsManager');
        
        if (!$this->_canHandle($providerName, $videoId, $ioc, $tpom)) {
            return false;
        }
        
        $theme        = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $template     = $theme->getTemplateInstance($this->_getTemplatePath($providerName, $videoId, $ioc, $tpom));

        $fullscreen      = $tpom->get(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $playerColor     = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR), '999999');
        $playerHighlight = org_tubepress_impl_embedded_EmbeddedPlayerUtils::getSafeColorValue($tpom->get(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT), 'FFFFFF');
        $autoPlay        = $tpom->get(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);

        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL, $this->_getEmbeddedDataUrl($providerName, $videoId, $ioc, $tpom));
        $template->setVariable(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, $tubepress_base_url);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH, $tpom->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT, $tpom->get(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT));
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY, $playerColor);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT, $playerHighlight);
        $template->setVariable(org_tubepress_api_const_template_Variable::EMBEDDED_FULLSCREEN, org_tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($fullscreen));

        $context->setReturnValue($template->toString());

        return true;
    }
    
    protected abstract function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    protected abstract function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);

    protected abstract function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_options_OptionsManager $tpom);
}
