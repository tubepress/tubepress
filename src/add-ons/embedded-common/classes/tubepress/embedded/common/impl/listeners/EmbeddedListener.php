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

class tubepress_embedded_common_impl_listeners_EmbeddedListener
{
    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_spi_embedded_EmbeddedProviderInterface[]
     */
    private $_embeddedProviders;

    public function __construct(tubepress_api_options_ContextInterface     $context,
                                tubepress_api_template_TemplatingInterface $templating)
    {
        $this->_context    = $context;
        $this->_templating = $templating;
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $toAdd = array();

        foreach ($this->_embeddedProviders as $provider) {

            $toAdd[$provider->getName()] = $provider->getUntranslatedDisplayName();
        }

        $toAdd = array_merge($current, $toAdd);
        ksort($toAdd);
        $toAdd                                                                       = array_reverse($toAdd, true);
        $toAdd[tubepress_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED] = 'Provider default';  //>(translatable)<
        $toAdd                                                                       = array_reverse($toAdd, true);

        $event->setSubject(array_merge($current, $toAdd));
    }

    public function onEmbeddedTemplateSelect(tubepress_api_event_EventInterface $event)
    {
        if (!$event->hasArgument('embeddedProvider')) {

            return;
        }

        /*
         * @var tubepress_spi_embedded_EmbeddedProviderInterface
         */
        $embeddedProvider = $event->getArgument('embeddedProvider');

        $event->setSubject($embeddedProvider->getTemplateName());
    }

    public function onPlayerTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        $this->onSingleItemTemplatePreRender($event);
    }

    public function onSingleItemTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var array
         */
        $existingTemplateVars = $event->getSubject();

        if (!isset($existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM])) {

            return;
        }

        /*
         * @var tubepress_api_media_MediaItem
         */
        $mediaItem        = $existingTemplateVars[tubepress_api_template_VariableNames::MEDIA_ITEM];
        $embeddedProvider = $this->_selectEmbeddedProvider($mediaItem);
        $embedWidth       = $this->_context->get(tubepress_api_options_Names::EMBEDDED_WIDTH);
        $embedHeight      = $this->_context->get(tubepress_api_options_Names::EMBEDDED_HEIGHT);
        $responsive       = $this->_context->get(tubepress_api_options_Names::RESPONSIVE_EMBEDS);
        $templateVars     = $embeddedProvider->getTemplateVariables($mediaItem);

        $embeddedHtml = $this->_templating->renderTemplate('single/embedded', array_merge(array(

            'embeddedProvider'                                       => $embeddedProvider,
            tubepress_api_template_VariableNames::MEDIA_ITEM         => $mediaItem,
            tubepress_api_template_VariableNames::EMBEDDED_WIDTH_PX  => $embedWidth,
            tubepress_api_template_VariableNames::EMBEDDED_HEIGHT_PX => $embedHeight,
            tubepress_api_options_Names::RESPONSIVE_EMBEDS           => $responsive,
        ), $templateVars));

        $existingTemplateVars[tubepress_api_template_VariableNames::EMBEDDED_SOURCE]    = $embeddedHtml;
        $existingTemplateVars[tubepress_api_template_VariableNames::EMBEDDED_WIDTH_PX]  = $embedWidth;
        $existingTemplateVars[tubepress_api_template_VariableNames::EMBEDDED_HEIGHT_PX] = $embedHeight;
        $existingTemplateVars[tubepress_api_options_Names::RESPONSIVE_EMBEDS]           = $responsive;

        $event->setSubject($existingTemplateVars);
    }

    public function onGalleryInitJs(tubepress_api_event_EventInterface $event)
    {
        $args             = $event->getSubject();
        $optionsToAdd     = array();
        $optionNamesToAdd = array(

            tubepress_api_options_Names::EMBEDDED_HEIGHT,
            tubepress_api_options_Names::EMBEDDED_WIDTH,
            tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL,
        );

        foreach ($optionNamesToAdd as $optionName) {

            $optionsToAdd[$optionName] = $this->_context->get($optionName);
        }

        if (!isset($args['options']) || !is_array($args['options'])) {

            $args['options'] = array();
        }

        $args['options'] = array_merge($args['options'], $optionsToAdd);

        $event->setSubject($args);
    }

    /**
     * @param tubepress_spi_embedded_EmbeddedProviderInterface[] $providers
     */
    public function setEmbeddedProviders(array $providers)
    {
        $this->_embeddedProviders = $providers;
    }

    /**
     * @param tubepress_api_media_MediaItem $item
     *
     * @return tubepress_spi_embedded_EmbeddedProviderInterface
     *
     * @throws RuntimeException
     */
    private function _selectEmbeddedProvider(tubepress_api_media_MediaItem $item)
    {
        $requestedEmbeddedPlayerName = $this->_context->get(tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL);

        /*
         * @var tubepress_spi_media_MediaProviderInterface
         */
        $mediaProvider     = $item->getAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER);
        $mediaProviderName = $mediaProvider->getName();

        /*
         * The user has requested a specific embedded player that is registered. Let's see if the provider agrees.
         */
        if ($requestedEmbeddedPlayerName !== tubepress_api_options_AcceptableValues::EMBEDDED_IMPL_PROVIDER_BASED) {

            foreach ($this->_embeddedProviders as $embeddedProvider) {

                $compatibleProviderNames = $embeddedProvider->getCompatibleMediaProviderNames();

                if ($embeddedProvider->getName() === $requestedEmbeddedPlayerName &&
                    in_array($mediaProviderName, $compatibleProviderNames)) {

                    //found it!
                    return $embeddedProvider;
                }
            }
        }

        /*
         * Do we have an embedded provider whose name exactly matches the provider? If so, let's use that. This
         * should be the common case.
         */
        foreach ($this->_embeddedProviders as $embeddedProvider) {

            if ($embeddedProvider->getName() === $mediaProviderName) {

                return $embeddedProvider;
            }
        }

        /*
         * Running out of options. See if we can find *any* player that can handle videos from this provider.
         */
        foreach ($this->_embeddedProviders as $embeddedProvider) {

            if (in_array($mediaProviderName, $embeddedProvider->getCompatibleMediaProviderNames())) {

                return $embeddedProvider;
            }
        }

        /*
         * None of the registered embedded players support the calculated provider. I give up.
         */
        throw new RuntimeException(sprintf('No embedded providers could generate HTML for item %s', $item->getId()));
    }
}
