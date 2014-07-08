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
 * Base class for fields that are directly modeled by an option provider.
 */
abstract class tubepress_app_options_ui_impl_fields_provided_AbstractProvidedOptionBasedField extends tubepress_app_options_ui_impl_fields_AbstractTemplateBasedOptionsPageField
{
    /**
     * @var tubepress_app_options_api_ReferenceInterface
     */
    private $_optionProvider;

    public function __construct($optionName,
                                tubepress_lib_translation_api_TranslatorInterface   $translator,
                                tubepress_app_options_api_PersistenceInterface      $persistence,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_app_options_api_ReferenceInterface         $optionProvider)
    {
        $this->_optionProvider = $optionProvider;

        if (!$this->_optionProvider->optionExists($optionName)) {

            throw new InvalidArgumentException(sprintf('Could not find option with name "%s"', $optionName));
        }

        $label       = $this->_optionProvider->getUntranslatedLabel($optionName);
        $description = $this->_optionProvider->getUntranslatedDescription($optionName);

        parent::__construct(

            $optionName,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $label,
            $description
        );
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return $this->_optionProvider->isProOnly($this->getId());
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public function onSubmit()
    {
        $id        = $this->getId();
        $isBoolean = $this->_optionProvider->isBoolean($this->getId());

        if ($isBoolean) {

            return $this->sendToStorage($id, $this->getHttpRequestParameters()->hasParam($id));
        }

        if (! $this->getHttpRequestParameters()->hasParam($id)) {

            /* not submitted. */
            return null;
        }

        $value = $this->getHttpRequestParameters()->getParamValue($id);

        return $this->sendToStorage($id, $this->convertIncomingStringValueToStorageFormat($value));
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $id      = $this->getId();
        $value   = $this->convertStorageFormatToStringValueForHTML($this->getOptionPersistence()->fetch($id));

        return array_merge(array(

            'id'    => $id,
            'value' => $value,

        ), $this->getAdditionalTemplateVariables());
    }

    /**
     * @return tubepress_app_options_api_ReferenceInterface
     */
    protected function getOptionProvider()
    {
        return $this->_optionProvider;
    }

    /**
     * @return array An associative array of additional template variables for this field (besides name and value)
     */
    protected function getAdditionalTemplateVariables()
    {
        //override point
        return array();
    }

    protected function convertIncomingStringValueToStorageFormat($incomingStringValue)
    {
        //override point
        return $incomingStringValue;
    }

    /**
     * @param mixed $storageData The
     *
     * @return string The data suitable for display in an HTML field.
     */
    protected function convertStorageFormatToStringValueForHTML($storageData)
    {
        //override point
        return $storageData;
    }
}