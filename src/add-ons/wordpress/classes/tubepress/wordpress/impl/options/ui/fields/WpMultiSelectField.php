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

class tubepress_wordpress_impl_options_ui_fields_WpMultiSelectField extends tubepress_options_ui_impl_fields_templated_multi_AbstractMultiSelectField
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_wordpress_impl_wp_ResourceRepository
     */
    private $_resourceRepo;

    public function __construct($id, $untranslatedDisplayName, $untranslatedDescription,
                                tubepress_api_options_PersistenceInterface      $persistence,
                                tubepress_api_http_RequestParametersInterface   $requestParams,
                                tubepress_api_template_TemplatingInterface      $templating,
                                tubepress_wordpress_impl_wp_WpFunctions         $wpFunctions,
                                tubepress_wordpress_impl_wp_ResourceRepository  $resourceRepo)
    {
        parent::__construct(
            $id,
            $persistence,
            $requestParams,
            $templating,
            $untranslatedDisplayName,
            $untranslatedDescription
        );

        $this->_wpFunctions  = $wpFunctions;
        $this->_resourceRepo = $resourceRepo;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCurrentlySelectedValues()
    {
        return explode(',', $this->getOptionPersistence()->fetch($this->getId()));
    }

    /**
     * {@inheritdoc}
     */
    protected function onSubmitAllMissing()
    {
        $this->getOptionPersistence()->queueForSave(
            $this->getId(),
            null
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUngroupedChoicesArray()
    {
        if ($this->getId() === tubepress_wordpress_api_Constants::OPTION_AUTOPOST_CATEGORIES) {

            $terms = $this->_resourceRepo->getAllCategories();

        } else {

            $terms = $this->_resourceRepo->getAllTags();
        }

        $toReturn = array();

        foreach ($terms as $term) {

            $toReturn[$term->slug] = $term->name;
        }

        return $toReturn;
    }

    /**
     * {@inheritdoc}
     */
    protected function onSubmitMixed(array $values)
    {
        $toSave = implode(',', $values);

        $this->getOptionPersistence()->queueForSave($this->getId(), $toSave);
    }

    /**
     * {@inheritdoc}
     */
    public function isProOnly()
    {
        return true;
    }
}
