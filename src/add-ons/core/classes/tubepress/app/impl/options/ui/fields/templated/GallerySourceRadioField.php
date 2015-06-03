<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_app_impl_options_ui_fields_templated_GallerySourceRadioField extends tubepress_app_impl_options_ui_fields_templated_AbstractTemplatedField implements tubepress_app_api_options_ui_MultiSourceFieldInterface
{
    /**
     * @var tubepress_app_api_options_ui_FieldInterface
     */
    private $_additionalField;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var string
     */
    private $_multiSourcePrefix;

    public function __construct($modeName, $multiSourcePrefix,
                                tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_template_TemplatingInterface    $templating,
                                tubepress_app_api_options_ContextInterface        $context,
                                tubepress_app_api_options_ui_FieldInterface       $additionalField = null)
    {
        parent::__construct(

            $modeName,
            $persistence,
            $requestParams,
            $templating
        );

        $this->_additionalField   = $additionalField;
        $this->_context           = $context;
        $this->_multiSourcePrefix = $multiSourcePrefix;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/gallery-source-radio';
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $currentMode = $this->_context->get(tubepress_app_api_options_Names::GALLERY_SOURCE);

        return array(

            'modeName'                  => $this->getId(),
            'currentMode'               => $currentMode,
            'additionalFieldWidgetHtml' => isset($this->_additionalField) ? $this->_additionalField->getWidgetHTML() : '',
            'prefix'                    => $this->_multiSourcePrefix,
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

    public function getUntranslatedDescription()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getUntranslatedDescription();
        }

        return parent::getUntranslatedDescription();
    }

    public function getUntranslatedDisplayName()
    {
        if ($this->_additionalField) {

            return $this->_additionalField->getUntranslatedDisplayName();
        }

        return parent::getUntranslatedDisplayName();
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly()
    {
        return false;
    }

    /**
     * @param $prefix
     * @param tubepress_app_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_app_api_options_ui_FieldInterface
     */
    public function cloneForMultiSource($prefix, tubepress_app_api_options_PersistenceInterface $persistence)
    {
        $httpRequestParams = $this->getHttpRequestParameters();
        $templating        = $this->getTemplating();
        $context           = $this->_context;
        $additionalField   = null;

        if ($this->_additionalField && $this->_additionalField instanceof tubepress_app_api_options_ui_MultiSourceFieldInterface) {

            /**
             * @var $temp tubepress_app_api_options_ui_MultiSourceFieldInterface
             */
            $temp = $this->_additionalField;

            $additionalField = $temp->cloneForMultiSource($prefix, $persistence);
        }

        return new self($this->getId(), $prefix, $persistence, $httpRequestParams, $templating, $context, $additionalField);
    }
}