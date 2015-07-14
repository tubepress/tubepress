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
 * An event that is passed around during TubePress's execution.
 *
 * Oftentimes, different add-ons need to be notified of
 * certain events taking place during the execution of TubePress (e.g., notify prior to JS scripts being written to HTML
 * so additional JS can be added, or inform when the video gallery is about to be displayed so custom code can be
 * executed). The `EventInterface` allows for the event itself to be modified through the `getSubject` and `setSubject`
 * functions described below, as well as for the passing of related objects through the `getArgument` (`getArguments`)
 * and `setArgument` (`setArguments`) functions described below.
 *
 * @package TubePress\Event
 *
 * @api
 * @since 4.0.0
 *
 * @deprecated Use tubepress_api_event_EventInterface instead.
 */
interface tubepress_lib_api_event_EventInterface extends tubepress_api_event_EventInterface
{

}