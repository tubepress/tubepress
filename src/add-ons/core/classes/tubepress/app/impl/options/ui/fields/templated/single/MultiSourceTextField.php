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
 * Displays a standard text input.
 */
class tubepress_app_impl_options_ui_fields_templated_single_MultiSourceTextField extends tubepress_app_impl_options_ui_fields_templated_single_TextField implements tubepress_app_api_options_ui_MultiSourceFieldInterface
{
    /**
     * @param $prefix
     * @param tubepress_app_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_app_api_options_ui_FieldInterface
     */
    public function cloneForMultiSource($prefix, tubepress_app_api_options_PersistenceInterface $persistence)
    {
        $optionName      = $this->getOptionName();
        $requestParams   = $this->getHttpRequestParameters();
        $templating      = $this->getTemplating();
        $optionReference = $this->getOptionProvider();

        $toReturn = new self($optionName, $persistence, $requestParams, $templating, $optionReference);

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}