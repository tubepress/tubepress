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
interface tubepress_app_impl_options_ui_fields_templated_multi_MediaProviderFieldInterface
{
    function getAllChoices();

    function getUntranslatedLabelForChoice($choice);

    function getMediaProviders();

    function providerRecognizesChoice(tubepress_app_api_media_MediaProviderInterface $mp, $choice);
}