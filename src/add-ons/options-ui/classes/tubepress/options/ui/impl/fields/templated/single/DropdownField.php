<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a drop-down input.
 */
class tubepress_options_ui_impl_fields_templated_single_DropdownField extends tubepress_options_ui_impl_fields_templated_single_SingleOptionField
{
    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_api_options_AcceptableValuesInterface
     */
    private $_acceptableValues;

    public function __construct($optionName,
                                tubepress_api_options_PersistenceInterface      $persistence,
                                tubepress_api_http_RequestParametersInterface   $requestParams,
                                tubepress_api_options_ReferenceInterface        $optionsReference,
                                tubepress_api_template_TemplatingInterface      $templating,
                                tubepress_api_util_LangUtilsInterface           $langUtils,
                                tubepress_api_options_AcceptableValuesInterface $acceptableValues)
    {
        parent::__construct(

            $optionName,
            'options-ui/fields/dropdown',
            $persistence,
            $requestParams,
            $templating,
            $optionsReference
        );

        $this->_langUtils        = $langUtils;
        $this->_acceptableValues = $acceptableValues;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalTemplateVariables()
    {
        $map = $this->_acceptableValues->getAcceptableValues($this->getOptionName());

        if (!$this->_langUtils->isAssociativeArray($map)) {

            throw new InvalidArgumentException(sprintf('"%s" has a non-associative array set for its value map', $this->getId()));
        }

        return array('ungroupedChoices' => $map);
    }

    /**
     * @return tubepress_api_util_LangUtilsInterface
     */
    protected function getLangUtils()
    {
        return $this->_langUtils;
    }

    /**
     * @return tubepress_api_options_AcceptableValuesInterface
     */
    protected function getAcceptableValues()
    {
        return $this->_acceptableValues;
    }
}
