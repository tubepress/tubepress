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
 *
 */
class tubepress_core_options_ui_impl_ElementBuilder implements tubepress_core_options_ui_api_ElementBuilderInterface
{
    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_translation_api_TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * Builds a new element.
     *
     * @param string $id                      The unique ID of this field.
     * @param string $untranslatedDisplayName An optional array of options to contruct the field.
     *
     * @return tubepress_core_options_ui_api_ElementInterface A new instance of the field.
     *
     * @api
     * @since 4.0.0
     */
    public function newInstance($id, $untranslatedDisplayName = null)
    {
        return new tubepress_core_options_ui_impl_BaseElement($id, $this->_translator, $untranslatedDisplayName);
    }
}