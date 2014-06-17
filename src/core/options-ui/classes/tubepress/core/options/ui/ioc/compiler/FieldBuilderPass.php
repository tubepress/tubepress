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
 *
 */
class tubepress_core_options_ui_ioc_compiler_FieldBuilderPass implements tubepress_api_ioc_CompilerPassInterface
{

    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!$containerBuilder->hasDefinition(tubepress_core_options_ui_api_FieldBuilderInterface::_)) {

            return;
        }

        $registryServiceIds = $containerBuilder->findTaggedServiceIds(tubepress_api_contrib_RegistryInterface::_);

        foreach ($registryServiceIds as $serviceId => $tags) {

            foreach ($tags as $tagData) {

                if (!isset($tagData['type'])) {

                    continue;
                }

                if ($tagData['type'] !== 'tubepress_core_theme_api_ThemeInterface') {

                    continue;
                }

                $fieldBuilderDef = $containerBuilder->getDefinition(tubepress_core_options_ui_api_FieldBuilderInterface::_);

                $fieldBuilderDef->addMethodCall('setThemeRegistry', array(new tubepress_api_ioc_Reference($serviceId)));
                return;
            }
        }
    }
}