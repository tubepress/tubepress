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
abstract class tubepress_app_options_ui_impl_fields_provided_AbstractSortField extends tubepress_app_options_ui_impl_fields_AbstractProviderBasedDropdownField
{
    /**
     * @var tubepress_app_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var
     */
    private $_optionsReference;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var array
     */
    private $_acceptableValues;

    public function __construct($fieldId,
                                tubepress_lib_translation_api_TranslatorInterface   $translator,
                                tubepress_app_options_api_PersistenceInterface      $persistence,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_app_options_api_ReferenceInterface        $optionsReference,
                                tubepress_app_options_api_ContextInterface          $context,
                                tubepress_app_options_api_AcceptableValuesInterface $acceptableValues,
                                tubepress_platform_api_util_LangUtilsInterface               $langUtils,
                                array                                               $mediaProviders,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct(

            $fieldId,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $mediaProviders,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_mediaProviders   = $mediaProviders;
        $this->_optionsReference = $optionsReference;
        $this->_context          = $context;
        $this->_acceptableValues = $acceptableValues->getAcceptableValues($fieldId);
        $this->_langUtils        = $langUtils;
    }

    /**
     * @return string The absolute path to the template for this field.
     */
    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/groupedDropdown.tpl.php';
    }

    /**
     * @return string[] An array of currently selected values, which may be empty.
     */
    protected function getCurrentlySelectedValues()
    {
        return array(

            $this->_context->get($this->getId())
        );
    }

    /**
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitAllMissing()
    {
        throw new LogicException();
    }

    /**
     * @param array $values The incoming values for this field.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    protected function onSubmitMixed(array $values)
    {
        foreach ($values as $value) {

            return $this->sendToStorage($this->getId(), $value);
        }

        return null;
    }

    protected function getAllChoices()
    {
        return array_keys($this->_acceptableValues);
    }

    protected function getUntranslatedLabelForChoice($choice)
    {
        return $this->_acceptableValues[$choice];
    }
}