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

class tubepress_http_oauth2_impl_Oauth2Environment implements tubepress_api_http_oauth2_Oauth2EnvironmentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRedirectionUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        throw new LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationInitiationUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        throw new LogicException();
    }

    /**
     * {@inheritdoc}
     */
    public function getCsrfSecret()
    {
        throw new LogicException();
    }
}
