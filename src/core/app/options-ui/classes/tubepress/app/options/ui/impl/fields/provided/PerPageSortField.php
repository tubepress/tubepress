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
class tubepress_app_options_ui_impl_fields_provided_PerPageSortField extends tubepress_app_options_ui_impl_fields_provided_AbstractSortField
{
    private static $_COMMON_SORTS = array(

        tubepress_app_media_provider_api_Constants::PER_PAGE_SORT_NONE,
        tubepress_app_media_provider_api_Constants::PER_PAGE_SORT_RANDOM,
    );

    public function __construct(tubepress_lib_translation_api_TranslatorInterface   $translator,
                                tubepress_app_options_api_PersistenceInterface      $persistence,
                                tubepress_app_http_api_RequestParametersInterface   $requestParams,
                                tubepress_lib_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_lib_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_app_options_api_ReferenceInterface        $optionsReference,
                                tubepress_app_options_api_ContextInterface          $context,
                                tubepress_app_options_api_AcceptableValuesInterface $acceptableValues,
                                tubepress_platform_api_util_LangUtilsInterface               $langUtils,
                                array                                               $mediaProviders)
    {
        parent::__construct(

            tubepress_app_media_provider_api_Constants::OPTION_PER_PAGE_SORT,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $templateFactory,
            $optionsReference,
            $context,
            $acceptableValues,
            $langUtils,
            $mediaProviders,
            'Per-page sort order',                //>(translatable)<
            'Additional sort order applied to each individual page of a gallery'  //>(translatable)<
        );
    }

    protected function providerRecognizesChoice(tubepress_app_media_provider_api_MediaProviderInterface $provider, $choice)
    {
        if (in_array($choice, self::$_COMMON_SORTS)) {

            return true;
        }

        $map = $provider->getMapOfPerPageSortNamesToUntranslatedLabels();

        return isset($map[$choice]);
    }
}