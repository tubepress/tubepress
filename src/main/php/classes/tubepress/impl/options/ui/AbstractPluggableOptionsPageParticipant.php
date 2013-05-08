<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

abstract class tubepress_impl_options_ui_AbstractPluggableOptionsPageParticipant implements tubepress_spi_options_ui_PluggableOptionsPageParticipant
{
    /**
     * @param string $tabName The name of the tab being built.
     *
     * @return array An array of fields that should be shown on the given tab. May be empty, never null.
     */
    public final function getFieldsForTab($tabName)
    {
        $raw = $this->doGetFieldsForTab($tabName);

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $fieldsEvent = new tubepress_spi_event_EventBase($raw,
            array('tabName' => $tabName, 'participant' => $this));
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::OPTIONS_UI_FIELDS_FOR_TAB, $fieldsEvent);

        return $fieldsEvent->getSubject();
    }

    protected abstract function doGetFieldsForTab($tabName);
}
