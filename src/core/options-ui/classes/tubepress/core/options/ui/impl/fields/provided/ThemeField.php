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
class tubepress_core_options_ui_impl_fields_provided_ThemeField extends tubepress_core_options_ui_impl_fields_provided_DropdownField
{
    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    /**
     * @var tubepress_core_theme_api_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function __construct(tubepress_core_translation_api_TranslatorInterface   $translator,
                                tubepress_core_options_api_PersistenceInterface      $persistence,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_options_api_ReferenceInterface        $optionProvider,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory,
                                tubepress_api_util_LangUtilsInterface                $langUtils,
                                tubepress_api_contrib_RegistryInterface              $themeRegistry,
                                tubepress_core_theme_api_ThemeLibraryInterface       $themeLibrary,
                                tubepress_core_options_api_AcceptableValuesInterface $acceptableValues)
    {
        parent::__construct(

            tubepress_core_theme_api_Constants::OPTION_THEME,
            $translator,
            $persistence,
            $requestParams,
            $eventDispatcher,
            $optionProvider,
            $templateFactory,
            $langUtils,
            $acceptableValues
        );

        $this->_themeLibrary  = $themeLibrary;
        $this->_themeRegistry = $themeRegistry;
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
        $themes   = $this->_themeRegistry->getAll();
        $toReturn = array();

        foreach ($themes as $theme) {

            $toReturn[$theme->getName()] = $this->_buildThemeData($theme);
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

    private function _buildThemeData(tubepress_core_theme_api_ThemeInterface $theme)
    {
        return array(

            'screenshots'   => $this->_themeLibrary->getScreenshots($theme->getName()),
            'description'   => $theme->getDescription(),
            'author'        => $theme->getAuthor(),
            'licenses'      => $theme->getLicenses(),
            'version'       => $theme->getVersion(),
            'demo'          => $theme->getDemoUrl(),
            'keywords'      => $theme->getKeywords(),
            'homepage'      => $theme->getHomepageUrl(),
            'docs'          => $theme->getDocumentationUrl(),
            'download'      => $theme->getDownloadUrl(),
            'bugs'          => $theme->getBugTrackerUrl(),
        );
    }
}