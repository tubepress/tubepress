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
 * Displays a standard text input.
 */
class tubepress_options_ui_impl_fields_templated_single_TextField extends tubepress_options_ui_impl_fields_templated_single_SingleOptionField
{
    /**
     * @var int The size of this text field.
     */
    private $_size = 20;

    public function __construct($optionName,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_options_ReferenceInterface      $optionReference)
    {
        parent::__construct($optionName,
                            'options-ui/fields/text',
                            $persistence,
                            $requestParams,
                            $templating,
                            $optionReference
        );
    }

    public function setSize($size)
    {
        if (intval($size) < 1) {

            throw new InvalidArgumentException('Text fields must have a non-negative size.');
        }

        $this->_size = intval($size);
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalTemplateVariables()
    {
        return array(

            'size' => $this->_size,
        );
    }
}
