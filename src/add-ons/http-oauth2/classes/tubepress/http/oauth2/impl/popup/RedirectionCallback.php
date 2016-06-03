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

class tubepress_http_oauth2_impl_popup_RedirectionCallback extends tubepress_http_oauth2_impl_popup_AbstractPopupHandler
{
    /**
     * This method will be (indirectly) invoked by the remote OAuth2 provider after we have initiated
     * authorization.
     *
     * 1. Check to ensure the presence of a request parameter named "provider" which is
     *    the name of a loaded OAuth2 provider.
     * 2. Check to ensure the presence of a request parameter named "code", which is the authorization code.
     * 3. Check for the presence of a request parameter named "state", and if present that its value matches
     *    our stored state for this provider.
     * 4. Clears the stored state.
     * 5. Fetches an access token from the token endpoint.
     * 6. Generates a slug for the new token.
     * 7. Saves the new token to the DB.
     * 8. Renders a success page with the token slug for use by the UI.
     */
    protected function execute()
    {
        $provider = $this->_getProvider();

        $this->validateState($provider);
        $this->clearState($provider);

        $code  = $this->getRequestParams()->getParamValue('code');
        $token = $this->getAccessTokenFetcher()->fetchWithCodeGrant($provider, $code);
        $slug  = $provider->getSlugForToken($token);

        $this->getPersistenceHelper()->saveToken($provider, $slug, $token);

        $this->renderSuccess('finish', 'Successfully connected to %s', $provider, array(
            'slug' => $slug,
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredParamNames()
    {
        return array(

            'code',
        );
    }

    /**
     * @return tubepress_spi_http_oauth2_Oauth2ProviderInterface
     */
    private function _getProvider()
    {
        $providerName = $this->getRequestParams()->getParamValue('provider');

        return $this->getProviderByName($providerName);
    }
}
