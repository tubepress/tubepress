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
 * Displays a color-chooser input.
 */
class tubepress_options_ui_impl_fields_templated_single_SpectrumColorField extends tubepress_options_ui_impl_fields_templated_single_SingleOptionField
{
    /**
     * @var string
     */
    private $_preferredFormat = 'hex';

    /**
     * @var bool
     */
    private $_showAlpha = false;

    /**
     * @var bool
     */
    private $_showInput = true;

    /**
     * @var bool
     */
    private $_showSelectionPalette = true;

    public function __construct($optionName,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_options_ReferenceInterface      $optionReference)
    {
        parent::__construct(
            $optionName, 'options-ui/fields/spectrum-color',
            $persistence, $requestParams, $templating, $optionReference
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalTemplateVariables()
    {
        return array(

            'preferredFormat' => $this->_preferredFormat,
            'showAlpha'       => $this->_showAlpha,
            'showInput'       => $this->_showInput,
            'showPalette'     => $this->_showSelectionPalette,
            'cancelText'      => 'cancel',
            'chooseText'      => 'Choose',
        );
    }

    public function setPreferredFormatToName()
    {
        $this->_preferredFormat = 'name';
    }

    public function setPreferredFormatToRgb()
    {
        $this->_preferredFormat = 'rgb';
    }

    public function setPreferredFormatToHex()
    {
        $this->_preferredFormat = 'hex';
    }

    /**
     * @param bool $showAlpha
     */
    public function setShowAlpha($showAlpha)
    {
        $this->_showAlpha = (boolean) $showAlpha;
    }

    /**
     * @param bool $showInput
     */
    public function setShowInput($showInput)
    {
        $this->_showInput = (boolean) $showInput;
    }

    /**
     * @param bool $showSelectionPalette
     */
    public function setShowSelectionPalette($showSelectionPalette)
    {
        $this->_showSelectionPalette = (boolean) $showSelectionPalette;
    }
}
