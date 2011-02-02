<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
tubepress_load_classes(array('org_tubepress_api_html_HtmlHandler',
    'org_tubepress_api_querystring_QueryStringService',
    'org_tubepress_impl_ioc_IocContainer'));

/**
 * HTML handler implementation.
 */
class org_tubepress_impl_html_DefaultHtmlHandler implements org_tubepress_api_html_HtmlHandler
{
    function getHeadJqueryIncludeString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\" src=\"$tubepress_base_url/ui/lib/jquery-1.4.2.min.js\"></script>";
    }

    function getHeadInlineJavaScriptString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\">function getTubePressBaseUrl(){return \"$tubepress_base_url\";}</script>";
    }

    function getHeadTubePressJsIncludeString()
    {
        global $tubepress_base_url;
        return "<script type=\"text/javascript\" src=\"$tubepress_base_url/ui/lib/tubepress.js\"></script>";
    }

    function getHeadTubePressCssIncludeString()
    {
        global $tubepress_base_url;
        return "<link rel=\"stylesheet\" href=\"$tubepress_base_url/ui/themes/default/style.css\" type=\"text/css\" />";
    }

    function getHeadMetaString()
    {
        global $tubepress_base_url;
        
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance(); 
        $qss  = $ioc->get('org_tubepress_api_querystring_QueryStringService');
        $page = $qss->getPageNum($_GET);    

        return $page > 1 ? "<meta name=\"robots\" content=\"noindex, nofollow\" />" : '';
    }
}
