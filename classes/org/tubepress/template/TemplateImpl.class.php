<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('net_php_pear_HTML_Template_IT',
    'org_tubepress_util_StringUtils'));

/**
 * TubePress template implementation. Just wraps net_php_pear_HTML_Template_IT()
 */
class org_tubepress_template_TemplateImpl implements org_tubepress_template_Template
{
    private $_tpl;
    private $_file;
    
    public function getHtml()
    {
        $this->_preFlightChecks();
        $html = $this->_tpl->get();
        
        /* do some housecleaning for the next guy */
        $this->_initTemplate();
        return org_tubepress_util_StringUtils::removeEmptyLines($html);
    }
    
    public function parse($block)
    {
        $this->_preFlightChecks();
        $this->_tpl->parse($block);
    }
    
    public function setFile($file)
    {
        $this->_file = $file;
        $this->_initTemplate();
    }
    
    public function setVariable($name, $value)
    {
        $this->_preFlightChecks();
        $this->_tpl->setVariable($name, $value);
    }
    
    private function _initTemplate()
    {
        $this->_tpl = new net_php_pear_HTML_Template_IT(dirname($this->_file));
        if (!$this->_tpl->loadTemplatefile(basename($this->_file), true, true)) {
            throw new Exception("Couldn't load template at " . $this->_file);
        }
    }
    
    private function _preFlightChecks()
    {
        if (!isset($this->_file)) {
            throw new Exception('No template file has been set');
        }
    }
}
