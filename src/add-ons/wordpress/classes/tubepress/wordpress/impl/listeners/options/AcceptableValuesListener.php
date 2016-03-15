<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_listeners_options_AcceptableValuesListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    public function __construct(tubepress_wordpress_impl_wp_WpFunctions $wpFunctions)
    {
        $this->_wpFunctions = $wpFunctions;
    }

    public function onWpPostCategories(tubepress_api_event_EventInterface $event)
    {
        $result     = array();
        $categories = $this->_wpFunctions->get_categories(array(
            'hide_empty' => false,
        ));

        foreach ($categories as $category) {

            $result[$category->slug] = $category->name;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostTags(tubepress_api_event_EventInterface $event)
    {
        $result = array();
        $tags   = $this->_wpFunctions->get_tags(array(
            'hide_empty' => false,
        ));

        foreach ($tags as $tag) {

            $result[$tag->slug] = $tag->name;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostTemplate(tubepress_api_event_EventInterface $event)
    {
        $result           = array();
        $templates        = $this->_wpFunctions->get_page_templates();
        $defaultTemplates = array(
            'default' => 'index.php',
        );
        $templates = array_merge($templates, $defaultTemplates);

        foreach ($templates as $displayName => $fileName) {

            $result[$fileName] = "$displayName ($fileName)";
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostType(tubepress_api_event_EventInterface $event)
    {
        $result = array();
        $types  = $this->_wpFunctions->get_post_types(array('public' => true));

        foreach ($types as $type) {

            $result[$type] = $type;
        }

        unset($result['attachment']);

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostStatus(tubepress_api_event_EventInterface $event)
    {
        $result   = array();
        $statuses = $this->_wpFunctions->get_post_stati();

        foreach ($statuses as $status) {

            $result[$status] = $status;
        }

        unset($result['auto-draft']);
        unset($result['inherit']);

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpUser(tubepress_api_event_EventInterface $event)
    {
        $result   = array();
        $allUsers = $this->_wpFunctions->get_users(array('who' => 'author'));

        foreach ($allUsers as $user) {

            $loginName = $user->user_login;
            $display   = $user->display_name;

            $result[$loginName] = $display;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    private function _sortArrayAndSetAsSubject(array $array, tubepress_api_event_EventInterface $event)
    {
        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        asort($array);

        $toSet = array_merge($current, $array);

        $event->setSubject($toSet);
    }
}