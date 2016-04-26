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

/**
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_options_ui_impl_fields_templated_single_ThemeField extends tubepress_options_ui_impl_fields_templated_single_DropdownField
{
    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_themeRegistry;

    public function __construct(tubepress_api_options_PersistenceInterface      $persistence,
                                tubepress_api_http_RequestParametersInterface   $requestParams,
                                tubepress_api_options_ReferenceInterface        $optionReference,
                                tubepress_api_template_TemplatingInterface      $templating,
                                tubepress_api_util_LangUtilsInterface           $langUtils,
                                tubepress_api_contrib_RegistryInterface         $themeRegistry,
                                tubepress_api_options_AcceptableValuesInterface $acceptableValues)
    {
        parent::__construct(

            tubepress_api_options_Names::THEME,
            $persistence,
            $requestParams,
            $optionReference,
            $templating,
            $langUtils,
            $acceptableValues
        );

        $this->_themeRegistry = $themeRegistry;
    }

    /**
     * {@inheritdoc}
     */
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

    private function _buildThemeData(tubepress_api_theme_ThemeInterface $theme)
    {
        $raw = array(

            'authors'     => $theme->getAuthors(),
            'description' => $theme->getDescription(),
            //'keywords'      => $theme->getKeywords(),
            'license'     => $theme->getLicense(),
            'screenshots' => $theme->getScreenshots(),
            'support'     => array(
                'demo'       => $theme->getDemoUrl(),
                'homepage'   => $theme->getHomepageUrl(),
                'docs'       => $theme->getDocumentationUrl(),
                'download'   => $theme->getDownloadUrl(),
                'bugs'       => $theme->getBugTrackerUrl(),
                'forum'      => $theme->getForumUrl(),
                'sourceCode' => $theme->getSourceCodeUrl(),
            ),
            'version' => $theme->getVersion(),
        );

        return $this->_deepToString($raw);
    }

    private function _deepToString($element)
    {
        if (is_array($element)) {

            foreach ($element as $key => $value) {

                $element[$key] = $this->_deepToString($value);
            }

            return $element;
        }

        if ($element instanceof tubepress_api_collection_MapInterface) {

            $keys     = $element->keySet();
            $toReturn = array();

            foreach ($keys as $key) {

                $toReturn[$key] = $this->_deepToString($element->get($key));
            }

            return $toReturn;
        }

        return "$element";
    }
}
