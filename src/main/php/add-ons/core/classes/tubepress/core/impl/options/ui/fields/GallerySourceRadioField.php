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
class tubepress_core_impl_options_ui_fields_GallerySourceRadioField extends tubepress_core_impl_options_ui_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @var tubepress_core_api_options_ui_FieldInterface
     */
    private $_additionalField;

    /**
     * @var tubepress_core_api_options_ContextInterface
     */
    private $_context;

    public function __construct($modeName,
                                tubepress_core_api_translation_TranslatorInterface      $translator,
                                tubepress_core_api_options_PersistenceInterface         $persistence,
                                tubepress_core_api_http_RequestParametersInterface      $requestParams,
                                tubepress_core_api_event_EventDispatcherInterface       $eventDispatcher,
                                tubepress_core_api_template_TemplateFactoryInterface    $templateFactory,
                                tubepress_core_api_options_ContextInterface             $context,
                                tubepress_core_api_options_ui_FieldInterface            $additionalField = null)
    {
        parent::__construct(

            $modeName,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory
        );

        $this->_additionalField = $additionalField;
        $this->_context         = $context;
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
        $currentMode = $this->_context->get(tubepress_core_api_const_options_Names::GALLERY_SOURCE);

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
        if (!isset($this->_additionalField)) {

            return null;
        }

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