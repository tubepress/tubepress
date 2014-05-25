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
 * Advanced option names.
 *
 * @package TubePress\Const\IoC
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_const_ioc_Tags
{
    const EVENT_LISTENER = 'tubepress.event.listener';

    const TAGGED_SERVICES_CONSUMER = 'tubepress.consumer.taggedServices';

    const TAGGED_SERVICE_CONSUMER     = 'tubepress.consumer.taggedService';

    const LTRIM_SUBJECT_LISTENER = 'tubepress.ltrimSubjectListener';

    const OPTIONS_PAGE_TEMPLATE = 'tubepress.options.pageTemplate';
}
