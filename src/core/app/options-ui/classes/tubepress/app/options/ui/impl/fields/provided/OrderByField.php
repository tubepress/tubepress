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
class tubepress_app_options_ui_impl_fields_provided_OrderByField extends tubepress_app_options_ui_impl_fields_provided_AbstractSortField
{
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

            tubepress_app_media_provider_api_Constants::OPTION_ORDER_BY,
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
            'Order videos by',                    //>(translatable)<
            sprintf('Not all sort orders can be applied to all gallery types. See the <a href="%s" target="_blank">documentation</a> for more info.', "http://docs.tubepress.com/page/reference/options/core.html#orderby")  //>(translatable)<
        );
    }

    protected function providerRecognizesChoice(tubepress_app_media_provider_api_MediaProviderInterface $provider, $choice)
    {
        $map = $provider->getMapOfFeedSortNamesToUntranslatedLabels();

        return isset($map[$choice]);
    }
}