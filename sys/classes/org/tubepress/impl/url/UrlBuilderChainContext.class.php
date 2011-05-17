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
class org_tubepress_impl_url_UrlBuilderChainContext extends org_tubepress_impl_patterns_cor_AbstractChainContext
{
    private $_providerName;
    
    private $_isSingle;

    private $_arg;
    
    public function __construct($providerName, $single, $arg)
    {
        $this->_providerName = $providerName;
        $this->_isSingle     = $single == true;
        $this->_arg          = $arg;
    }
    
    public function getArg()
    {
        return $this->_arg;
    }

    public function isSingle()
    {
        return $this->_isSingle === true;
    }
    
    public function getProviderName()
    {
        return $this->_providerName;
    }
}
