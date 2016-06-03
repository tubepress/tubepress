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
 * Displays a drop-down input.
 */
class tubepress_options_ui_impl_fields_templated_single_MultiSourceDropdownField extends tubepress_options_ui_impl_fields_templated_single_DropdownField implements tubepress_api_options_ui_MultiSourceFieldInterface
{
    /**
     * {@inheritdoc}
     */
    public function cloneForMultiSource($prefix, tubepress_api_options_PersistenceInterface $persistence)
    {
        $optionName       = $this->getOptionName();
        $requestParams    = $this->getHttpRequestParameters();
        $optionsReference = $this->getOptionProvider();
        $templating       = $this->getTemplating();
        $langUtils        = $this->getLangUtils();
        $acceptableValues = $this->getAcceptableValues();

        $toReturn = new self($optionName, $persistence, $requestParams, $optionsReference, $templating, $langUtils, $acceptableValues);

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}
