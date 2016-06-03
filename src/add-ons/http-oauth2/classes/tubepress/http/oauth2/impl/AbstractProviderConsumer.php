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

abstract class tubepress_http_oauth2_impl_AbstractProviderConsumer
{
    /**
     * @var tubepress_spi_http_oauth2_Oauth2ProviderInterface[]
     */
    private $_providers;

    protected function ensureProvidersAvailable()
    {
        if (!isset($this->_providers) || count($this->_providers) === 0) {

            throw new RuntimeException('No OAuth2 providers available.');
        }
    }

    /**
     * @return tubepress_spi_http_oauth2_Oauth2ProviderInterface
     */
    protected function getProviderByName($providerName)
    {
        $actualProvider = null;

        foreach ($this->_providers as $provider) {

            if ($provider->getName() === $providerName) {

                $actualProvider = $provider;
                break;
            }
        }

        if (!$actualProvider) {

            throw new InvalidArgumentException('No such OAuth2 provider.');
        }

        return $actualProvider;
    }

    /**
     * @return tubepress_spi_http_oauth2_Oauth2ProviderInterface[]
     */
    protected function getAllProviders()
    {
        return $this->_providers;
    }

    public function setOauth2Providers(array $providers)
    {
        foreach ($providers as $provider) {

            if (!($provider instanceof tubepress_spi_http_oauth2_Oauth2ProviderInterface)) {

                throw new InvalidArgumentException('Non tubepress_spi_http_oauth2_Oauth2ProviderInterface in incoming providers.');
            }
        }

        $this->_providers = $providers;
    }
}
