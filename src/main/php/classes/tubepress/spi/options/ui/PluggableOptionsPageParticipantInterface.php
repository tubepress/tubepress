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
 * Major participant in the option page.
 */
interface tubepress_spi_options_ui_PluggableOptionsPageParticipantInterface extends tubepress_spi_options_ui_OptionsPageItemInterface
{
    /**
     * @return tubepress_spi_options_ui_OptionsPageItemInterface[] The categories that this participant supplies.
     */
    function getCategories();

    /**
     * @return tubepress_spi_options_ui_OptionsPageFieldInterface[] The fields that this options page participant provides.
     */
    function getFields();

    /**
     * @return array An associative array, which may be empty, where the keys are category IDs and the values
     *               are arrays of field IDs that belong in the category.
     */
    function getCategoryIdsToFieldIdsMap();
}