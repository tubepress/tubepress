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

/**
 * Base class for HTML fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractField implements tubepress_spi_options_ui_Field
{
    const TEMPLATE_VAR_NAME  = 'org_tubepress_impl_options_ui_fields_AbstractField__name';

    /** Message service. */
    private $_messageService;

    /** Option storage manager. */
    private $_storageManager;

    /** HTTP request param service. */
    private $_httpRequestParameterService;

    /** Template builder. */
    private $_templateBuilder;

    /** Environment detector. */
    private $_environmentDetector;

    public function __construct(

        tubepress_spi_message_MessageService           $messageService,
        tubepress_spi_http_HttpRequestParameterService $hrps,
        tubepress_spi_environment_EnvironmentDetector  $environmentDetector,
        ehough_contemplate_api_TemplateBuilder         $templateBuilder,
        tubepress_spi_options_StorageManager           $storageManager)
    {
        $this->_messageService              = $messageService;
        $this->_storageManager              = $storageManager;
        $this->_httpRequestParameterService = $hrps;
        $this->_environmentDetector         = $environmentDetector;
        $this->_templateBuilder             = $templateBuilder;
    }

    /**
     * Gets the title of this field, usually consumed by humans.
     *
     * @return string The title of this field. May be empty or null.
     */
    public final function getTitle()
    {
        return $this->_getMessage($this->getRawTitle());
    }

    /**
     * Gets the description of this field, usually consumed by humans.
     *
     * @return string The description of this field. May be empty or null.
     */
    public final function getDescription()
    {
        $originalDescription = $this->_getMessage($this->getRawDescription());

        return $this->getModifiedDescription($originalDescription);
    }

    protected final function getMessageService()
    {
        return $this->_messageService;
    }

    protected final function getStorageManager()
    {
        return $this->_storageManager;
    }

    protected final function getHttpRequestParameterService()
    {
        return $this->_httpRequestParameterService;
    }

    protected final function getEnvironmentDetector()
    {
        return $this->_environmentDetector;
    }

    protected final function getTemplateBuilder()
    {
        return $this->_templateBuilder;
    }

    /**
     * Override point.
     *
     * Allows subclasses to further modify the description for this field.
     *
     * @param $originalDescription string The original description as calculated by AbstractField.php.
     *
     * @return string The (possibly) modified description for this field.
     */
    protected function getModifiedDescription($originalDescription)
    {
        //override point
        return $originalDescription;
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected abstract function getRawTitle();

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected abstract function getRawDescription();

    private function _getMessage($raw)
    {
        if ($raw == '') {

            return '';
        }

        return $this->_messageService->_($raw);
    }
}