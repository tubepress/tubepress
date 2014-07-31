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

class tubepress_test_integration_mocks_MockTranslator implements tubepress_lib_api_translation_TranslatorInterface
{

    /**
     * Get the message corresponding to the given key.
     *
     * @param string $messageKey The message key.
     *
     * @return string The corresponding message.
     *
     * @api
     * @since 4.0.0
     */
    public function _($messageKey)
    {
        return $messageKey;
    }
}