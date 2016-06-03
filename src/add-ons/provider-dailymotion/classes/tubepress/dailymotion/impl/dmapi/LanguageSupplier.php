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
class tubepress_dailymotion_impl_dmapi_LanguageSupplier extends tubepress_dailymotion_impl_dmapi_AbstractLanguageLocaleSupplier
{
    /**
     * {@inheritdoc}
     */
    protected function getDisplayNameFromCode($code, array $entry)
    {
        if (isset($entry['native_name'])) {

            return $entry['native_name'];
        }

        return $entry['name'];
    }
}
