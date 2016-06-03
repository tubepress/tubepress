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
 * Base class for multi-select fields.
 */
abstract class tubepress_options_ui_impl_fields_templated_multi_AbstractMultiSelectField extends tubepress_options_ui_impl_fields_templated_AbstractTemplatedField
{
    public function __construct($id,
                                tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_template_TemplatingInterface    $templating,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(

            $id,
            $persistence,
            $requestParams,
            $templating,
            $untranslatedDisplayName,
            $untranslatedDescription
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateName()
    {
        return 'options-ui/fields/multiselect';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTemplateVariables()
    {
        return array(

            'id'                      => $this->getId(),
            'currentlySelectedValues' => $this->getCurrentlySelectedValues(),
            'ungroupedChoices'        => $this->getUngroupedChoicesArray(),
            'groupedChoices'          => $this->getGroupedChoicesArray(),
            "selectText"              => 'select ...',     //>(translatable)<
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onSubmit()
    {
        $id = $this->getId();

        if (!$this->getHttpRequestParameters()->hasParam($id)) {

            return $this->onSubmitAllMissing();
        }

        $vals = $this->getHttpRequestParameters()->getParamValue($id);

        if (!is_array($vals)) {

            /* this should never happen. */
            return null;
        }

        return $this->onSubmitMixed($vals);
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected abstract function getCurrentlySelectedValues();

    /**
     * @return array An associative array of value => untranslated display names
     */
    protected abstract function getUngroupedChoicesArray();

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected abstract function onSubmitAllMissing();

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected abstract function onSubmitMixed(array $values);

    /**
     * @return array An associative array of translated group names to associative array of
     *               value => untranslated display names
     */
    protected function getGroupedChoicesArray()
    {
        //override point
        return array();
    }
}
