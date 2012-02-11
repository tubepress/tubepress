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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_spi_options_ui_Field'
));

/**
 * Base class for HTML fields.
 */
abstract class org_tubepress_impl_options_ui_fields_AbstractField implements org_tubepress_spi_options_ui_Field
{
    const TEMPLATE_VAR_NAME  = 'org_tubepress_impl_options_ui_fields_AbstractField__name';

    /** Message service. */
    private $_messageService;

    public function __construct()
    {
        $ioc                   = org_tubepress_impl_ioc_IocContainer::getInstance();
        $this->_messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
    }

    public function getTitle()
    {
        return $this->_getMessage($this->getRawTitle());
    }

    public function getDescription()
    {
        return $this->_getMessage($this->getRawDescription());
    }

    protected function getMessageService()
    {
        return $this->_messageService;
    }

    protected abstract function getRawTitle();

    protected abstract function getRawDescription();

    private function _getMessage($raw)
    {
        if ($raw == '') {

            return '';
        }

        return $this->_messageService->_($raw);
    }
}