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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_http_ParamName',
    'org_tubepress_api_html_HeadHtmlGenerator',
    'org_tubepress_api_http_HttpRequestParameterService',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Generates HTML for use in the <head>.
 */
class org_tubepress_impl_html_DefaultHeadHtmlGenerator implements org_tubepress_api_html_HeadHtmlGenerator
{
    private $_tubepressBaseUrl;

    public function __construct()
    {
        global $tubepress_base_url;
        $this->_tubepressBaseUrl = $tubepress_base_url;
    }

    public function getHeadJqueryInclusion()
    {
        $url = $this->_tubepressBaseUrl;
        return "<script type=\"text/javascript\" src=\"$url/sys/ui/static/js/jquery-1.7.1.min.js\"></script>";
    }

    public function getHeadInlineJs()
    {
        $url = $this->_tubepressBaseUrl;
        return "<script type=\"text/javascript\">function getTubePressBaseUrl(){return \"$url\";}</script>";
    }

    public function getHeadJsIncludeString()
    {
        $url = $this->_tubepressBaseUrl;
        return "<script type=\"text/javascript\" src=\"$url/sys/ui/static/js/tubepress.js\"></script>";
    }

    public function getHeadCssIncludeString()
    {
        $url = $this->_tubepressBaseUrl;
        return "<link rel=\"stylesheet\" href=\"$url/sys/ui/themes/default/style.css\" type=\"text/css\" />";
    }

    public function getHeadHtmlMeta()
    {
        $ioc  = org_tubepress_impl_ioc_IocContainer::getInstance();
        $qss  = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);
        $page = $qss->getParamValueAsInt(org_tubepress_api_const_http_ParamName::PAGE, 1);

        return $page > 1 ? "<meta name=\"robots\" content=\"noindex, nofollow\" />" : '';
    }
}
