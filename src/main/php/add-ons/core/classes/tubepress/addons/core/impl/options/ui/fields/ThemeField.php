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
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_addons_core_impl_options_ui_fields_ThemeField extends tubepress_impl_options_ui_fields_DropdownField
{
    public function __construct(tubepress_api_options_PersistenceInterface $persistence,
                                tubepress_api_translation_TranslatorInterface $translator,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct(tubepress_api_const_options_names_Thumbs::THEME, $translator, $persistence, $eventDispatcher);
    }

    protected function getAdditionalTemplateVariables()
    {
        $vars         = parent::getAdditionalTemplateVariables();
        $choicesArray = $vars['choices'];

        asort($choicesArray);
        uasort($choicesArray, array($this, '__callbackSortChoices'));

        $vars['choices'] = $choicesArray;

        return $vars;
    }

    /**
     * @return string
     */
    public function getThemeDataAsJson()
    {
        $themeFinder  = tubepress_impl_patterns_sl_ServiceLocator::getThemeFinder();
        $themeHandler = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $themes       = $themeFinder->findAllThemes();
        $toReturn     = array();

        foreach ($themes as $theme) {

            $toReturn[$theme->getName()] = $this->_buildThemeData($theme, $themeHandler);
        }

        $asJson = json_encode($toReturn);

        return htmlspecialchars($asJson, ENT_NOQUOTES);
    }

    public function __callbackSortChoices($a, $b)
    {
        $aIsLegacy = strpos($a, '(legacy)') !== false;
        $bIsLegacy = strpos($b, '(legacy)') !== false;

        if ($aIsLegacy && $bIsLegacy) {

            return $a > $b;
        }

        if ($aIsLegacy) {

            return 1;
        }

        if ($bIsLegacy) {

            return -1;
        }

        return $a > $b;
    }

    private function _buildThemeData(tubepress_spi_theme_ThemeInterface $theme, tubepress_spi_theme_ThemeHandlerInterface $handler)
    {
        return array(

            'screenshots'   => $handler->getScreenshots($theme->getName()),
            'description'   => $theme->getDescription(),
            'author'        => $theme->getAuthor(),
            'licenses'      => $theme->getLicenses(),
            'version'       => $theme->getVersion()->__toString(),
            'demo'          => $theme->getDemoUrl(),
            'keywords'      => $theme->getKeywords(),
            'homepage'      => $theme->getHomepageUrl(),
            'docs'          => $theme->getDocumentationUrl(),
            'download'      => $theme->getDownloadUrl(),
            'bugs'          => $theme->getBugTrackerUrl(),
        );
    }
}