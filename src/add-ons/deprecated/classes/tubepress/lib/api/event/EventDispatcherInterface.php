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
 * The dispatcher that passes events around during TubePress's execution. allows add-ons to listen
 * for and respond to specific events occuring during different phases of TubePress code execution.
 *
 * @package TubePress\Event
 *
 * @api
 * @since 4.0.0
 *
 * @deprecated Use tubepress_api_event_EventDispatcherInterface instead.
 */
interface tubepress_lib_api_event_EventDispatcherInterface extends tubepress_api_event_EventDispatcherInterface
{

}