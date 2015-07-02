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
 *
 */
class tubepress_app_impl_options_ui_fields_templated_single_OrderByField extends tubepress_app_impl_options_ui_fields_templated_single_MultiSourceDropdownField
{
    public function __construct(tubepress_app_api_options_PersistenceInterface      $persistence,
                                tubepress_lib_api_http_RequestParametersInterface   $requestParams,
                                tubepress_lib_api_template_TemplatingInterface      $templating,
                                tubepress_app_api_options_ReferenceInterface        $optionsReference,
                                tubepress_app_api_options_AcceptableValuesInterface $acceptableValues,
                                tubepress_platform_api_util_LangUtilsInterface      $langUtils)
    {
        parent::__construct(

            tubepress_app_api_options_Names::FEED_ORDER_BY,
            $persistence,
            $requestParams,
            $optionsReference,
            $templating,
            $langUtils,
            $acceptableValues
        );

        $this->setProperty(self::$PROPERTY_UNTRANS_DISPLAY_NAME, 'Order videos by');            //>(translatable)<
        $this->setProperty(self::$PROPERTY_UNTRANS_DESCRIPTION, sprintf('Not all sort orders can be applied to all gallery types. See the <a href="%s" target="_blank">documentation</a> for more info.', "http://docs.tubepress.com/page/reference/options/core.html#orderby"));  //>(translatable)<

    }

    protected function providerRecognizesChoice(tubepress_app_api_media_MediaProviderInterface $provider, $choice)
    {
        $map = $provider->getMapOfFeedSortNamesToUntranslatedLabels();

        return isset($map[$choice]);
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

    /**
     * @param $prefix
     * @param tubepress_app_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_app_api_options_ui_FieldInterface
     */
    public function cloneForMultiSource($prefix, tubepress_app_api_options_PersistenceInterface $persistence)
    {
        $requestParams    = $this->getHttpRequestParameters();
        $optionsReference = $this->getOptionProvider();
        $templating       = $this->getTemplating();
        $langUtils        = $this->getLangUtils();
        $acceptableValues = $this->getAcceptableValues();

        $toReturn = new self($persistence, $requestParams, $templating, $optionsReference, $acceptableValues, $langUtils);

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}