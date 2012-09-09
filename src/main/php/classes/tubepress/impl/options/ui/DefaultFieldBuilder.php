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
 * Builds fields with reflection.
 */
class tubepress_impl_options_ui_DefaultFieldBuilder implements tubepress_spi_options_ui_FieldBuilder
{
    private $_optionsReference;

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

        tubepress_spi_message_MessageService            $messageService,
        tubepress_spi_http_HttpRequestParameterService  $hrps,
        tubepress_spi_environment_EnvironmentDetector   $environmentDetector,
        ehough_contemplate_api_TemplateBuilder          $templateBuilder,
        tubepress_spi_options_StorageManager            $storageManager,
        tubepress_spi_options_OptionDescriptorReference $reference)
    {
        $this->_messageService              = $messageService;
        $this->_storageManager              = $storageManager;
        $this->_httpRequestParameterService = $hrps;
        $this->_environmentDetector         = $environmentDetector;
        $this->_templateBuilder             = $templateBuilder;
        $this->_optionsReference            = $reference;
    }

    /**
     * Build a single field with the given name and type.
     *
     * @param string $name The name of the field to build.
     * @param string $type The name of the class to construct to represent this field.
     *
     * @return tubepress_spi_options_ui_Field The constructed field.
     */
    public final function build($name, $type)
    {
        $ref = new ReflectionClass($type);

        return $ref->newInstance($name);
    }

    /**
     * Builds the multi-select dropdown for meta display.
     *
     * @return tubepress_impl_options_ui_fields_MetaMultiSelectField The constructed field.
     */
    public final function buildMetaDisplayMultiSelectField()
    {
        return new tubepress_impl_options_ui_fields_MetaMultiSelectField(

            $this->_messageService,
            $this->_httpRequestParameterService,
            $this->_environmentDetector,
            $this->_templateBuilder,
            $this->_storageManager,
            $this->_optionsReference
        );
    }
}
