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
 * Displays a color-chooser input.
 */
class tubepress_app_options_ui_impl_fields_provided_SpectrumColorField extends tubepress_app_options_ui_impl_fields_provided_AbstractProvidedOptionBasedField
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

    protected function getAdditionalTemplateVariables()
    {
        $cancelText = $this->translate('cancel');
        $chooseText = $this->translate('Choose');

        return array(

            'preferredFormat' => $this->_preferredFormat,
            'showAlpha'       => $this->_showAlpha,
            'showInput'       => $this->_showInput,
            'showPalette'     => $this->_showSelectionPalette,
            'cancelText'      => $cancelText,
            'chooseText'      => $chooseText,
        );
    }

    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/spectrum-color.tpl.php';
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
     * @param boolean $showAlpha
     */
    public function setShowAlpha($showAlpha)
    {
        $this->_showAlpha = (boolean) $showAlpha;
    }

    /**
     * @param boolean $showInput
     */
    public function setShowInput($showInput)
    {
        $this->_showInput = (boolean) $showInput;
    }

    /**
     * @param boolean $showSelectionPalette
     */
    public function setShowSelectionPalette($showSelectionPalette)
    {
        $this->_showSelectionPalette = (boolean) $showSelectionPalette;
    }
}