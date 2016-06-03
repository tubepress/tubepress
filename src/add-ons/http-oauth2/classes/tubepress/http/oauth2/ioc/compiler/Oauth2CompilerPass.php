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

class tubepress_http_oauth2_ioc_compiler_Oauth2CompilerPass implements tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        if (!$containerBuilder->hasDefinition(tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_)) {
            
            $containerBuilder->register(
                tubepress_api_http_oauth2_Oauth2EnvironmentInterface::_,
                'tubepress_http_oauth2_impl_Oauth2Environment'
            );
        }
    }
}
