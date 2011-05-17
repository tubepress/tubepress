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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_plugin_FilterPoint',
    'org_tubepress_api_patterns_cor_Chain',
    'org_tubepress_api_plugin_PluginManager',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_api_shortcode_ShortcodeHtmlGenerator',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext',
));

/**
 * HTML handler implementation.
 */
class org_tubepress_impl_shortcode_DefaultShortcodeHtmlGenerator implements org_tubepress_api_shortcode_ShortcodeHtmlGenerator
{
    const LOG_PREFIX = 'HTML Generator';
    
    private $_ioc;
    
    private $_tpom;
    
    private $_chain;
    
    private $_tubepressBaseUrl;

    public function __construct()
    {
        global $tubepress_base_url;
        
        $this->_tubepressBaseUrl = $tubepress_base_url;
        $this->_ioc              = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_tpom             = $this->_ioc->get('org_tubepress_api_options_OptionsManager');
        $this->_chain            = $this->_ioc->get('org_tubepress_api_patterns_cor_Chain');
    }

    protected function _getShortcodeCommands()
    {
        return array(
            'org_tubepress_impl_shortcode_commands_SearchInputCommand',
            'org_tubepress_impl_shortcode_commands_SearchOutputCommand',
            'org_tubepress_impl_shortcode_commands_SingleVideoCommand',
            'org_tubepress_impl_shortcode_commands_SoloPlayerCommand',
            'org_tubepress_impl_shortcode_commands_ThumbGalleryCommand'
        );
    }
    
    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param string $shortCodeContent The shortcode content.
     *
     * @return The HTML for the given shortcode, or the error message if there was a problem.
     */
    public function getHtmlForShortcode($shortCodeContent)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pm  = $ioc->get('org_tubepress_api_plugin_PluginManager');

        /* do a bit of logging */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Type of IOC container is %s', get_class($ioc));

        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {
            $shortcodeParser = $ioc->get('org_tubepress_api_shortcode_ShortcodeParser');
            $shortcodeParser->parse($shortCodeContent);
        }

        /* use the command manager to get the HTML */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Running the shortcode HTML chain');
        $rawHtml = $this->_runChain();
        
        /* send it through the filters */
        if ($pm->hasFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_ANY)) {            
            return $pm->runFilters(org_tubepress_api_const_plugin_FilterPoint::HTML_ANY, $rawHtml);
        }
        
        return $rawHtml;
    }
    
    private function _runChain()
    {
        $context = new org_tubepress_impl_shortcode_ShortcodeHtmlGenerationChainContext();
        $status  = $this->_chain->execute($context, $this->_getShortcodeCommands());

        if ($status === false) {
            throw new Exception('No commands could handle execution.');
        }
        
        return $context->getReturnValue();
    }
}
