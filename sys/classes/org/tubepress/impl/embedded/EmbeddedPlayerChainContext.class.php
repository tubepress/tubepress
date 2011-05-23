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
    'org_tubepress_impl_patterns_cor_AbstractChainContext',
));

/**
 * The context for the embedded player chain.
 */
class org_tubepress_impl_embedded_EmbeddedPlayerChainContext
{
    private $_providerName;
    
    private $_videoId;
    
    private $_template;
    
    private $_embeddedImplName;
    
    private $_dataUrl;
    
    public function __construct($providerName, $videoId)
    {
        $this->_providerName = $providerName;
        $this->_videoId      = $videoId;
    }
    
    public function setDataUrl(org_tubepress_api_url_Url $url)
    {
        $this->_dataUrl = $url;
    }
    
    public function setEmbeddedImplementationName($name)
    {
        $this->_embeddedImplName = $name;
    }
    
    public function setTemplate(org_tubepress_api_template_Template $template)
    {
        $this->_template = $template;
    }
    
    public function getVideoId()
    {
        return $this->_videoId;
    }
    
    public function getProviderName()
    {
        return $this->_providerName;
    }
    
    public function getDataUrl()
    {
        return $this->_dataUrl;
    }
    
    public function getEmbeddedImplementationName()
    {
        return $this->_embeddedImplName;
    }
    
    public function getTemplate()
    {
        return $this->_template;
    }
}