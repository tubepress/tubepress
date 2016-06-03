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

class tubepress_options_ui_impl_fields_templated_multi_MediaProviderFieldHelper
{
    /**
     * @return array An associative array of translated group names to associative array of
     *               value => translated display names
     */
    public static function getGroupedChoicesArray(tubepress_options_ui_impl_fields_templated_multi_MediaProviderFieldInterface $mpfi)
    {
        $mapOfChoicesToUntranslatedMediaProviderNames = array();

        foreach ($mpfi->getAllChoices() as $choice) {

            if (!isset($mapOfChoicesToUntranslatedMediaProviderNames[$choice])) {

                $mapOfChoicesToUntranslatedMediaProviderNames[$choice] = array();
            }

            foreach ($mpfi->getMediaProviders() as $mediaProvider) {

                if ($mpfi->providerRecognizesChoice($mediaProvider, $choice)) {

                    $displayName = $mediaProvider->getDisplayName();

                    if (!in_array($displayName, $mapOfChoicesToUntranslatedMediaProviderNames[$choice])) {

                        $mapOfChoicesToUntranslatedMediaProviderNames[$choice][] = $displayName;
                    }
                }
            }
        }

        $middleMap = array();

        foreach ($mapOfChoicesToUntranslatedMediaProviderNames as $choice => $providerDisplayNames) {

            $finalGroupLabel = implode(' / ', $providerDisplayNames);

            if (!isset($middleMap[$finalGroupLabel])) {

                $middleMap[$finalGroupLabel] = array();
            }

            $optionLabel = $mpfi->getUntranslatedLabelForChoice($choice);

            $middleMap[$finalGroupLabel][$choice] = $optionLabel;
        }

        /*
         * Sort within the groups.
         */
        foreach ($middleMap as $finalGroupLabel => $choices) {

            asort($choices);
            $middleMap[$finalGroupLabel] = $choices;
        }

        $sortedLabels = array_keys($middleMap);
        usort($sortedLabels, array('tubepress_options_ui_impl_fields_templated_multi_MediaProviderFieldHelper', '__sortByMostSlashes'));

        $finalMap = array();
        foreach ($sortedLabels as $finalGroupLabel) {

            $finalMap[$finalGroupLabel] = $middleMap[$finalGroupLabel];
        }

        return $finalMap;
    }

    public static function __sortByMostSlashes($a, $b)
    {
        $aCount = substr_count($a, '/');
        $bCount = substr_count($b, '/');

        if ($aCount > $bCount) {

            return -1;
        }

        if ($aCount === $bCount) {

            return 0;
        }

        return 1;
    }
}
