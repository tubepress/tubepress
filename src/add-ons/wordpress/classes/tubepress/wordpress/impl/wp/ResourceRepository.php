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

class tubepress_wordpress_impl_wp_ResourceRepository
{
    /**
     * @var tubepress_api_collection_MapInterface
     */
    private $_cache;

    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
        $this->_cache       = new tubepress_internal_collection_Map();
    }

    /**
     * @return stdClass[]
     */
    public function getAllUsablePostTypes()
    {
        if (!$this->_cache->containsKey('usablePostTypes')) {

            $toIgnore = array('attachment', 'nav_menu_item', 'revision');

            $types = $this->_wpFunctions->get_post_types(
                array('public' => true),
                'objects'
            );

            $toReturn = array();

            foreach ($types as $type) {

                if (!in_array($type->name, $toIgnore)) {

                    $toReturn[] = $type;
                }
            }

            $this->_cache->put('usablePostTypes', $toReturn);
        }

        return $this->_cache->get('usablePostTypes');
    }

    /**
     * @return stdClass[]
     */
    public function getAllUsablePostStatuses()
    {
        if (!$this->_cache->containsKey('usablePostStatuses')) {

            $toIgnore = array('auto-draft', 'inherit', 'future');

            $statuses = $this->_wpFunctions->get_post_stati(
                array(),
                'objects'
            );

            $toReturn = array();

            foreach ($statuses as $status) {

                if (!in_array($status->name, $toIgnore)) {

                    $toReturn[] = $status;
                }
            }

            $this->_cache->put('usablePostStatuses', $toReturn);
        }

        return $this->_cache->get('usablePostStatuses');
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @return WP_User[]
     */
    public function getAuthors()
    {
        if (!$this->_cache->containsKey('authors')) {

            $authors = $this->_wpFunctions->get_users(
                array(
                    'who' => 'author',
                )
            );

            $this->_cache->put('authors', $authors);
        }

        return $this->_cache->get('authors');
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @return WP_Term[]
     */
    public function getAllCategories()
    {
        if (!$this->_cache->containsKey('allCategories')) {

            $categories = $this->_wpFunctions->get_categories(
                array(
                    'hide_empty' => false,
                )
            );

            $this->_cache->put('allCategories', $categories);
        }

        return $this->_cache->get('allCategories');
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @return WP_Term[]
     */
    public function getAllTags()
    {
        if (!$this->_cache->containsKey('allTags')) {

            $tags = $this->_wpFunctions->get_tags(
                array(
                    'hide_empty' => false,
                )
            );

            $this->_cache->put('allTags', $tags);
        }

        return $this->_cache->get('allTags');
    }

    /**
     * @return array An associative array of filenames to template display names.
     */
    public function getPageTemplates()
    {
        if (!$this->_cache->containsKey('templates')) {

            $wpTheme  = $this->_wpFunctions->wp_get_theme();
            $toReturn = array();

            /* @noinspection PhpUndefinedMethodInspection */
            $templates = $wpTheme->get_page_templates();

            foreach ($templates as $displayName => $fileName) {

                $toReturn[$fileName] = $displayName;
            }

            $toReturn['index.php'] = 'default';

            asort($toReturn);

            $this->_cache->put('templates', $toReturn);
        }

        return $this->_cache->get('templates');
    }
}
