<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_lib_impl_http_oauth_v1_Client implements tubepress_lib_api_http_oauth_v1_ClientInterface
{
    /**
     * @param tubepress_lib_api_http_message_RequestInterface $request
     * @param tubepress_lib_api_http_oauth_v1_Credentials       $clientCredentials
     * @param tubepress_lib_api_http_oauth_v1_Credentials       $tokenCredentials
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function signRequest(tubepress_lib_api_http_message_RequestInterface     $request,
                                tubepress_lib_api_http_oauth_v1_Credentials $clientCredentials,
                                tubepress_lib_api_http_oauth_v1_Credentials $tokenCredentials = null)
    {
        $oAuthParams = $this->_getBaseOAuthParams($clientCredentials);

        if ($tokenCredentials !== null) {

            $oAuthParams['oauth_token'] = $tokenCredentials->getIdentifier();
        }

        $this->_sign($request, $oAuthParams, $clientCredentials, $tokenCredentials);
    }

    private function _getBaseOAuthParams(tubepress_lib_api_http_oauth_v1_Credentials $clientCredentials)
    {
        return array(

            'oauth_consumer_key'     => $clientCredentials->getIdentifier(),
            'oauth_nonce'            => md5(uniqid(mt_rand(), true)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp'        => time(),
            'oauth_version'          => '1.0',
        );
    }

    private function _sign(tubepress_lib_api_http_message_RequestInterface     $httpRequest,
                           array                                        $oauthParams,
                           tubepress_lib_api_http_oauth_v1_Credentials $clientCredentials,
                           tubepress_lib_api_http_oauth_v1_Credentials $tokenCredentials = null)
    {
        $oauthParams['oauth_signature'] = $this->_getSignature($httpRequest, $oauthParams, $clientCredentials, $tokenCredentials);
        $header                         = 'OAuth ';
        $delimiter                      = '';

        foreach ($oauthParams as $key => $value) {

            $header .= $delimiter . $this->_urlEncode($key) . '="' . $this->_urlEncode($value) . '"';

            $delimiter = ', ';
        }

        $httpRequest->setHeader('Authorization', $header);
    }

    private function _getSignature(tubepress_lib_api_http_message_RequestInterface     $request,
                                   array                                        $baseOAuthParams,
                                   tubepress_lib_api_http_oauth_v1_Credentials $clientCredentials,
                                   tubepress_lib_api_http_oauth_v1_Credentials $tokenCredentials = null)
    {
        $url                 = $request->getUrl();
        $existingQueryParams = $url->getQuery()->toArray();
        $signatureData       = array_merge($existingQueryParams, $baseOAuthParams);

        foreach ($signatureData as $key => $value) {

            $signatureData[$this->_urlEncode($key)] = $this->_urlEncode($value);
        }

        uksort($signatureData, 'strcmp');

        $baseUrl         = $url->getScheme() . '://' . $this->_getNormalizedAuthority($url) . $url->getPath();
        $baseStringParts = $this->_urlEncode(array(

            $request->getMethod(),
            $baseUrl,
            $this->_concatParams($signatureData)
        ));
        $baseString      = implode('&', $baseStringParts);
        $keyParts        = $this->_urlEncode(array(

            $clientCredentials->getSecret(),
            $tokenCredentials === null ? '' : $tokenCredentials->getSecret(),
        ));
        $signingKey      = implode('&', $keyParts);
        $signature       = base64_encode($this->_hash($baseString, $signingKey));

        return $signature;
    }

    private function _concatParams(array $params)
    {
        $toReturn  = '';
        $delimiter = '';

        foreach ($params as $key => $value) {

            $toReturn .= $delimiter . $key . '=' . $value;

            $delimiter = '&';
        }

        return $toReturn;
    }

    private function _getNormalizedAuthority(tubepress_platform_api_url_UrlInterface $url)
    {
        $scheme = $url->getScheme();
        $port   = $url->getPort();

        if ($port != null && (($scheme === 'http' && $port != 80)
                || ($scheme === 'https' && $port != 465))) {

            return $url->getAuthority();
        }

        return $url->getHost();
    }

    /**
     * URL encode a parameter or array of parameters.
     *
     * @param array|string $input A parameter or set of parameters to encode.
     *
     * @return array|string The URL encoded parameter or array of parameters.
     */
    private function _urlEncode($input)
    {
        if (is_array($input)) {

            return array_map(array($this, '_urlEncode'), $input);

        } elseif (is_scalar($input)) {

            return str_replace(array('+', '%7E'), array(' ', '~'), rawurlencode($input));

        } else {

            return '';
        }
    }

    private function _hash($data, $key)
    {
        return hash_hmac('sha1', $data, $key, true);
    }
}
