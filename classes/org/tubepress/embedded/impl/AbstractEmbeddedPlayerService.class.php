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
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_embedded_EmbeddedPlayerService',
    'org_tubepress_options_manager_OptionsManager',
    'org_tubepress_template_Template'));

/**
 * Represents an HTML-embeddable YouTube player
 *
 */
abstract class org_tubepress_embedded_impl_AbstractEmbeddedPlayerService implements org_tubepress_embedded_EmbeddedPlayerService
{   
    private $_optionsManager;
    protected $_template;
    
    protected function _safeColorValue($candidate, $default)
    {
        $pattern = '/^[0-9a-fA-F]{6}$/';
        if (preg_match($pattern, $candidate) === 1) {
            return $candidate;
        }
        return $default;
    }

    protected function booleanToOneOrZero($bool)
    {
        if ($bool === '1') {
            return '1';
        }
        if ($bool === '0') {
            return '0';
        }
        return $bool ? '1' : '0';
    }

    protected function booleanToString($bool)
    {
        return $bool ? 'true' : 'false';
    }
    
    public function setOptionsManager(org_tubepress_options_manager_OptionsManager $optionsManager) { $this->_optionsManager = $optionsManager; }
    public function setTemplate(org_tubepress_template_Template $template) { $this->_template = $template; }
    
    protected function getOptionsManager()
    {
        return $this->_optionsManager;
    }
}
