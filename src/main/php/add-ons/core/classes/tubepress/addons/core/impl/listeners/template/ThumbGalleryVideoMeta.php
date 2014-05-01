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
 * Handles applying video meta info to the gallery template.
 */
class tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta
{
    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(
        tubepress_api_options_ContextInterface $context,
        tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_translator = $translator;
        $this->_context    = $context;
    }

    public function onGalleryTemplate(tubepress_api_event_EventInterface $event)
    {
        $optionProvider = tubepress_impl_patterns_sl_ServiceLocator::getOptionProvider();

        /**
         * @var $metaNameService tubepress_addons_core_impl_options_MetaOptionNameService
         */
        $metaNameService = tubepress_impl_patterns_sl_ServiceLocator::getService(tubepress_addons_core_impl_options_MetaOptionNameService::_);
        $metaNames       = $metaNameService->getAllMetaOptionNames();
        $shouldShow      = array();
        $labels          = array();
        $template        = $event->getSubject();

        foreach ($metaNames as $metaName) {

            if (!$optionProvider->hasOption($metaName)) {

                $shouldShow[$metaName] = false;
                $labels[$metaName]     = '';
                continue;
            }

            $shouldShow[$metaName] = $this->_context->get($metaName);
            $labels[$metaName]     = $this->_translator->_($optionProvider->getLabel($metaName));
        }

        $template->setVariable(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_api_const_template_Variable::META_LABELS, $labels);
    }
}
