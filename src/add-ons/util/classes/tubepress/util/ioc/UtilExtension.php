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

class tubepress_util_ioc_UtilExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            tubepress_api_util_TimeUtilsInterface::_,
            'tubepress_util_impl_TimeUtils'
        )->addArgument(new tubepress_api_ioc_Reference(tubepress_api_util_StringUtilsInterface::_));

        $containerBuilder->register(
            tubepress_api_util_LangUtilsInterface::_,
            'tubepress_util_impl_LangUtils'
        );

        $containerBuilder->register(
            tubepress_api_util_StringUtilsInterface::_,
            'tubepress_util_impl_StringUtils'
        );
    }
}
