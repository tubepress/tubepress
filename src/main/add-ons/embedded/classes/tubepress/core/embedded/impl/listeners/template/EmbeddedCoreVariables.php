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
 * Core variables for the embedded template.
 */
class tubepress_core_embedded_impl_listeners_template_EmbeddedCoreVariables
{
    /**
     * @var tubepress_core_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_core_options_api_ContextInterface         $context,
                                tubepress_core_environment_api_EnvironmentInterface $environment)
    {
        $this->_context     = $context;
        $this->_environment = $environment;
    }

    public function onEmbeddedTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $template = $event->getSubject();
        $dataUrl  = $event->getArgument('dataUrl');
        $itemId   = $event->getArgument('itemId');

        /**
         * @var $mediaProvider tubepress_core_provider_api_MediaProviderInterface
         */
        $mediaProvider = $event->getArgument('mediaProvider');

        /**
         * @var $embeddedProvider tubepress_core_embedded_api_EmbeddedProviderInterface
         */
        $embeddedProvider = $event->getArgument('embeddedProvider');

        $autoPlay    = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_AUTOPLAY);
        $embedWidth  = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH);
        $embedHeight = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT);

        $vars = array(

            tubepress_core_template_api_const_VariableNames::EMBEDDED_DATA_URL   => $dataUrl->toString(),
            tubepress_core_template_api_const_VariableNames::TUBEPRESS_BASE_URL  => $this->_environment->getBaseUrl()->toString(),
            tubepress_core_template_api_const_VariableNames::EMBEDDED_AUTOSTART  => self::booleanToString($autoPlay),
            tubepress_core_template_api_const_VariableNames::EMBEDDED_WIDTH      => $embedWidth,
            tubepress_core_template_api_const_VariableNames::EMBEDDED_HEIGHT     => $embedHeight,
            tubepress_core_template_api_const_VariableNames::VIDEO_ID            => $itemId,
            tubepress_core_template_api_const_VariableNames::VIDEO_DOM_ID        => $this->_getVideoDomId($mediaProvider->getName(), $dataUrl),
            tubepress_core_template_api_const_VariableNames::EMBEDDED_IMPL_NAME  => $embeddedProvider->getName(),
            tubepress_core_template_api_const_VariableNames::VIDEO_PROVIDER_NAME => $mediaProvider->getName(),
        );

        foreach ($vars as $key => $value) {

            $template->setVariable($key, $value);
        }
    }

    private function _getVideoDomId($providerName, tubepress_core_url_api_UrlInterface $dataUrl)
    {
        if ($providerName !== 'vimeo') {

            return 'tubepress-video-object-' . mt_rand();
        }

        $query = $dataUrl->getQuery();

        if ($query->hasKey('player_id')) {

            return $query->get('player_id');
        }

        //this should never happen
        return 'tubepress-video-object-' . mt_rand();
    }

    /**
     * Returns a valid HTML color.
     *
     * @param string $candidate The first-choice HTML color. May be invalid.
     * @param string $default   The fallback HTML color. Must be be invalid.
     *
     * @return string $candidate if it's a valid HTML color. $default otherwise.
     */
    public static function getSafeColorValue($candidate, $default)
    {
        $pattern = '/^[0-9a-fA-F]{6}$/';

        if (preg_match($pattern, $candidate) === 1) {

            return $candidate;
        }

        return $default;
    }

    /**
     * Converts a boolean value to string.
     *
     * @param boolean $bool The boolean value to convert.
     *
     * @return string 'true' or 'false'
     */
    public static function booleanToString($bool)
    {
        return $bool ? 'true' : 'false';
    }
}