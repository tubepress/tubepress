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
 * Base class for fields that are directly modeled by an option provider.
 */
class tubepress_app_impl_options_ui_fields_templated_single_SingleOptionField extends tubepress_app_impl_options_ui_fields_templated_AbstractTemplatedField
{
    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionProvider;

    /**
     * @var string
     */
    private $_templateName;

    /**
     * @var string
     */
    private $_multiSourcePrefix = '';

    /**
     * @var string
     */
    private $_optionName;

    public function __construct($optionName, $templateName,
                                tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_template_TemplatingInterface    $templating,
                                tubepress_app_api_options_ReferenceInterface      $optionReference)
    {
        $this->_optionProvider    = $optionReference;
        $this->_templateName      = $templateName;
        $this->_optionName        = $optionName;

        if (!$this->_optionProvider->optionExists($optionName)) {

            throw new InvalidArgumentException(sprintf('Could not find option with name "%s"', $optionName));
        }

        $label       = $this->_optionProvider->getUntranslatedLabel($optionName);
        $description = $this->_optionProvider->getUntranslatedDescription($optionName);

        parent::__construct(

            $optionName,
            $persistence,
            $requestParams,
            $templating,
            $label,
            $description
        );
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return $this->_multiSourcePrefix . parent::getId();
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return $this->_optionProvider->isProOnly($this->_optionName);
    }

    /**
     * Handles form submission.
     *
     * @return array An array of failure messages if there's a problem, otherwise null.
     */
    public function onSubmit()
    {
        $isBoolean     = $this->_optionProvider->isBoolean($this->_optionName);
        $paramName     = $this->getId();
        $requestParams = $this->getHttpRequestParameters();

        if ($isBoolean) {

            return $this->sendToStorage($this->_optionName, $requestParams->hasParam($paramName));
        }

        if (!$requestParams->hasParam($paramName)) {

            /* not submitted. */
            return null;
        }

        $value = $requestParams->getParamValue($paramName);

        return $this->sendToStorage($this->_optionName, $this->convertIncomingStringValueToStorageFormat($value));
    }

    /**
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        $id      = $this->getId();
        $value   = $this->convertStorageFormatToStringValueForHTML($this->getOptionPersistence()->fetch($this->_optionName));

        return array_merge(array(

            'id'     => $id,
            'value'  => $value,
            'prefix' => $this->_multiSourcePrefix,

        ), $this->getAdditionalTemplateVariables());
    }

    /**
     * @return tubepress_app_api_options_ReferenceInterface
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

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getTemplateName()
    {
        return $this->_templateName;
    }

    protected function setMultiSourcePrefix($prefix)
    {
        $this->_multiSourcePrefix = $prefix;
    }

    /**
     * @return string
     */
    protected function getOptionName()
    {
        return $this->_optionName;
    }
}