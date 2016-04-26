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
class tubepress_template_impl_twig_EnvironmentBuilder
{
    /**
     * @var Twig_LoaderInterface
     */
    private $_loader;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettingsInterface;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    public function __construct(Twig_LoaderInterface                          $loader,
                                tubepress_api_boot_BootSettingsInterface      $bootSettings,
                                tubepress_api_options_ContextInterface        $context,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_loader                = $loader;
        $this->_bootSettingsInterface = $bootSettings;
        $this->_context               = $context;
        $this->_translator            = $translator;
    }

    public function buildTwigEnvironment()
    {
        $environment = new Twig_Environment($this->_loader, array(
            'cache'       => $this->_getCache(),
            'auto_reload' => $this->_getAutoReload(),
        ));

        $this->_addFilters($environment);

        return $environment;
    }

    private function _getAutoReload()
    {
        return (bool) $this->_context->get(tubepress_api_options_Names::TEMPLATE_CACHE_AUTORELOAD);
    }

    private function _getCache()
    {
        $enabled = $this->_context->get(tubepress_api_options_Names::TEMPLATE_CACHE_ENABLED);

        if (!$enabled) {

            return false;
        }

        $dir = $this->_context->get(tubepress_api_options_Names::TEMPLATE_CACHE_DIR);

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        $dir = $this->_bootSettingsInterface->getPathToSystemCacheDirectory() . DIRECTORY_SEPARATOR . '/twig';

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        @mkdir($dir);

        if ($this->_writableDirectory($dir)) {

            return $dir;
        }

        return false;
    }

    private function _writableDirectory($candidate)
    {
        return is_dir($candidate) && is_writable($candidate);
    }

    private function _addFilters(Twig_Environment $environment)
    {
        $transFilter       = new Twig_SimpleFilter('trans', array($this, '__callback_trans'));
        $transChoiceFilter = new Twig_SimpleFilter('transChoice', array($this, '__callback_transchoice'));

        $environment->addFilter('trans', $transFilter);
        $environment->addFilter('transChoice', $transChoiceFilter);
    }

    public function __callback_trans($message, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->_translator->trans($message, $arguments, $domain, $locale);
    }

    public function __callback_transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        return $this->_translator->transChoice($message, $count, array_merge(array('%count%' => $count), $arguments), $domain, $locale);
    }
}
