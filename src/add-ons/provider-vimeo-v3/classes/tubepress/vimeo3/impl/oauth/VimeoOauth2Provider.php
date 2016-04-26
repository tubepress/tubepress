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

class tubepress_vimeo3_impl_oauth_VimeoOauth2Provider implements tubepress_spi_http_oauth2_Oauth2ProviderInterface
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_stringUtils = $stringUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vimeoV3';
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return 'Vimeo';
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationGrantType()
    {
        return 'client_credentials';
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthorizationUrl(tubepress_api_url_UrlInterface $authorizationUrl,
                                       $clientId, $clientSecret = null)
    {
        //noop
    }

    /**
     * {@inheritdoc}
     */
    public function isStateUsed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenEndpoint()
    {
        return $this->_urlFactory->fromString('https://api.vimeo.com/oauth/authorize/client');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenType()
    {
        return 'bearer';
    }

    /**
     * {@inheritdoc}
     */
    public function onRefreshTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                          tubepress_api_http_oauth_v2_TokenInterface  $token,
                                          $clientId, $clientSecret = null)
    {
        //noop - Vimeo tokens are permanent
    }

    /**
     * {@inheritdoc}
     */
    public function getSlugForToken(tubepress_api_http_oauth_v2_TokenInterface $token)
    {
        $extraParams = $token->getExtraParams();

        if (isset($extraParams['user']) && isset($extraParams['user']['name'])) {

            $name = $extraParams['user']['name'];

            if (!isset($extraParams['scope'])) {

                return $name;
            }

            $scopeList = $extraParams['scope'];

            if (strpos($scopeList, 'private') !== false) {

                return "$name (All Access)";
            }

            return $name;
        }

        return 'Basic access (public videos only)';
    }

    /**
     * {@inheritdoc}
     */
    public function wantsToAuthorizeRequest(tubepress_api_http_message_RequestInterface $request)
    {
        $url = $request->getUrl();

        if ($url->getHost() !== 'api.vimeo.com') {

            return false;
        }

        $path      = $url->getPath();
        $oauthPath = $this->_stringUtils->startsWith($path, '/oauth');

        return !$oauthPath;
    }

    /**
     * {@inheritdoc}
     */
    public function authorizeRequest(tubepress_api_http_message_RequestInterface $request,
                                     tubepress_api_http_oauth_v2_TokenInterface  $token,
                                     $clientId, $clientSecret = null)
    {
        $request->setHeader('Authorization', 'bearer ' . $token->getAccessToken());
        $request->setHeader('Accept', 'application/vnd.vimeo.*+json;version=3.2');
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatedClientRegistrationInstructions(tubepress_api_translation_TranslatorInterface $translator,
                                                         tubepress_api_url_UrlInterface                $redirectUrl)
    {
        $step1 = $translator->trans('<a href="%client-registration-url%" target="_blank">Click here</a> to create a new Vimeo &quot;App&quot;.',  //>(translatable)<
            array(
                '%client-registration-url%' => 'https://developer.vimeo.com/apps/new',
            )
        );
        $step1Subs = array(

            $translator->trans('Use anything you\'d like for the App Name, App Description, and App URL.'),                 //>(translatable)<
            $translator->trans('In the field for &quot;App Callback URLs&quot;, enter:<br /><code>%redirect-uri%</code>',    //>(translatable)<
                array('%redirect-uri%' => $redirectUrl->toString())
            ),
        );
        $step2 = $translator->trans('Under the &quot;OAuth2&quot; tab of your new Vimeo App, you will find your &quot;Client Identifier&quot; and &quot;Client Secret&quot;. Enter those values into the text boxes below.');  //>(translatable)<

        $step3 = $translator->trans('Click the &quot;New token&quot; button below to authorize TubePress to communicate with Vimeo on your behalf. This step will take place in a popup window.');   //>(translatable)<

        return array(
            $step1,
            $step1Subs,
            $step2,
            $step3,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedTermForClientId()
    {
        return 'OAuth2 Client Identifier'; //>(translatable)<
    }

    /**
     * {@inheritdoc}
     */
    public function getUntranslatedTermForClientSecret()
    {
        return 'OAuth2 Client Secret'; //>(translatable)<
    }

    /**
     * {@inheritdoc}
     */
    public function isClientSecretUsed()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAccessTokenRequest(tubepress_api_http_message_RequestInterface $request,
                                  $clientId,
                                  $clientSecret = null)
    {
        $request->setHeader('Authorization', 'basic ' . base64_encode($clientId . ':' . $clientSecret));
    }
}
