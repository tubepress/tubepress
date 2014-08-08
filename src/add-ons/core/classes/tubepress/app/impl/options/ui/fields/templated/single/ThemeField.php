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
class tubepress_app_impl_options_ui_fields_templated_single_ThemeField extends tubepress_app_impl_options_ui_fields_templated_single_DropdownField
{
    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    public function __construct(tubepress_app_api_options_PersistenceInterface      $persistence,
                                tubepress_lib_api_http_RequestParametersInterface   $requestParams,
                                tubepress_app_api_options_ReferenceInterface        $optionReference,
                                tubepress_lib_api_template_TemplatingInterface      $templating,
                                tubepress_platform_api_util_LangUtilsInterface      $langUtils,
                                tubepress_platform_api_contrib_RegistryInterface    $themeRegistry,
                                tubepress_app_api_options_AcceptableValuesInterface $acceptableValues)
    {
        parent::__construct(

            tubepress_app_api_options_Names::THEME,
            $persistence,
            $requestParams,
            $optionReference,
            $templating,
            $langUtils,
            $acceptableValues
        );

        $this->_themeRegistry = $themeRegistry;
    }

    protected function getAdditionalTemplateVariables()
    {
        $vars         = parent::getAdditionalTemplateVariables();
        $choicesArray = $vars['ungroupedChoices'];

        asort($choicesArray);
        uasort($choicesArray, array($this, '__callbackSortChoices'));

        $vars['ungroupedChoices'] = $choicesArray;

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

    private function _buildThemeData(tubepress_app_api_theme_ThemeInterface $theme)
    {
        return array(

            'screenshots'   => $theme->getScreenshots(),
            'description'   => $theme->getDescription(),
            'authors'       => $theme->getAuthors(),
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