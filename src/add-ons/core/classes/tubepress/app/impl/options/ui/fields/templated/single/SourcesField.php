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
 * Displays a drop-down input.
 */
class tubepress_app_impl_options_ui_fields_templated_single_SourcesField extends tubepress_app_impl_options_ui_fields_templated_AbstractTemplatedField
{
    /**
     * @var array
     */
    private $_gallerySourceRadioFieldMap;

    /**
     * @var array
     */
    private $_feedFieldMap;

    /**
     * @var tubepress_app_api_options_ui_FieldProviderInterface[]
     */
    private $_fieldProviders;

    public function __construct(tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams,
                                tubepress_lib_api_template_TemplatingInterface    $templating,
                                array $gallerySourceRadioFieldMap,
                                array $feedFieldMap,
                                array $fieldProviders)
    {
        parent::__construct(tubepress_app_api_options_Names::SOURCES, $persistence, $requestParams, $templating);

        $this->_gallerySourceRadioFieldMap = $gallerySourceRadioFieldMap;
        $this->_feedFieldMap               = $feedFieldMap;
        $this->_fieldProviders             = $fieldProviders;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/sources/sources-field';
    }

    /**
     *
     *
     * @return array An associative array of template variables for this field.
     */
    protected function getTemplateVariables()
    {
        return array();
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     *
     * @api
     * @since 4.0.0
     */
    public function onSubmit()
    {
        // TODO: Implement onSubmit() method.
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
}