<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a single radio input.
 */
class tubepress_impl_options_ui_fields_GallerySourceRadioField extends tubepress_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @var string
     */
    private $_modeName;

    /**
     * @var tubepress_spi_options_ui_OptionsPageFieldInterface
     */
    private $_additionalField;

    public function __construct($modeName, tubepress_spi_options_ui_OptionsPageFieldInterface $additionalField = null)
    {
        parent::__construct($modeName);

        $this->_modeName        = $modeName;
        $this->_additionalField = $additionalField;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/gallery-source-radio.tpl.php';
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $context     = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $currentMode = $context->get(tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        return array(

            'modeName'                  => $this->getId(),
            'currentMode'               => $currentMode,
            'additionalFieldWidgetHtml' => isset($this->_additionalField) ? $this->_additionalField->getWidgetHTML() : '',
        );
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        return $this->_additionalField->onSubmit();
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return false;
    }

    public function getTranslatedDisplayName()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getTranslatedDisplayName();
        }

        return parent::getTranslatedDisplayName();
    }

    public function getTranslatedDescription()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getTranslatedDescription();
        }

        return parent::getTranslatedDescription();
    }

}