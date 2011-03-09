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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_html_HtmlGenerator',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_patterns_StrategyManager'));

/**
 * HTML handler implementation.
 */
class org_tubepress_impl_html_DefaultHtmlGenerator implements org_tubepress_api_html_HtmlGenerator
{
    const LOG_PREFIX = 'HTML Generator';

    /**
     * Generates the HTML for TubePress. Could be a gallery or single video.
     *
     * @param string $shortCodeContent The shortcode content. May be empty or null.
     *
     * @return The HTML for the given shortcode.
     */
    public function getHtmlForShortcode($shortCodeContent)
    {
        try {
            return $this->_wrappedGetHtmlForShortcode($shortCodeContent);
        } catch (Exception $e) {
            return $e->getMessage;
        }
    }
    
    private function _wrappedGetHtmlForShortcode($shortCodeContent)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        /* do a bit of logging */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Type of IOC container is %s', get_class($ioc));

        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {
            $shortcodeParser = $ioc->get('org_tubepress_api_shortcode_ShortcodeParser');
            $shortcodeParser->parse($shortCodeContent);
        }

        /* use the strategy manager to get the HTML */
        return $ioc->get('org_tubepress_api_patterns_StrategyManager')->executeStrategy($this->getStrategies());
    }

    public function getHeadJqueryIncludeString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\" src=\"$tubepress_base_url/sys/ui/static/js/jquery-1.5.1.min.js\"></script>";
    }

    public function getHeadInlineJavaScriptString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\">function getTubePressBaseUrl(){return \"$tubepress_base_url\";}</script>";
    }

    public function getHeadTubePressJsIncludeString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\" src=\"$tubepress_base_url/sys/ui/static/js/tubepress.js\"></script>";
    }

    public function getHeadTubePressCssIncludeString()
    {
        global $tubepress_base_url;
        return "<link rel=\"stylesheet\" href=\"$tubepress_base_url/sys/ui/themes/default/style.css\" type=\"text/css\" />";
    }

    public function getHeadMetaString()
    {
        global $tubepress_base_url;
        
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance(); 
        $qss  = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $page = $qss->getPageNum($_GET);    

        return $page > 1 ? "<meta name=\"robots\" content=\"noindex, nofollow\" />" : '';
    }
    
    protected function getStrategies()
    {
        return array(
            'org_tubepress_impl_html_strategies_SearchInputStrategy',
            'org_tubepress_impl_html_strategies_SearchOutputStrategy',
            'org_tubepress_impl_html_strategies_SingleVideoStrategy',
            'org_tubepress_impl_html_strategies_SoloPlayerStrategy',
            'org_tubepress_impl_html_strategies_ThumbGalleryStrategy'
        );
    }
}
