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
 * Initializes the options in storage.
 */
class tubepress_addons_core_impl_listeners_boot_OptionsStorageInitListener
{
    public function onBoot(tubepress_api_event_EventInterface $event)
    {
        $storageManager = tubepress_impl_patterns_sl_ServiceLocator::getOptionStorageManager();
        $odr            = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();
        $allOptions     = $odr->findAll();
        $toPersist      = array();

        /**
         * @var $optionDescriptor tubepress_spi_options_OptionDescriptor
         */
        foreach ($allOptions as $optionDescriptor) {

            if ($optionDescriptor->isMeantToBePersisted()) {

                $toPersist[$optionDescriptor->getName()] = $optionDescriptor->getDefaultValue();
            }
        }

        $storageManager->createEachIfNotExists($toPersist);
    }
}