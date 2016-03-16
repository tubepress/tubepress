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
     * @var tubepress_wordpress_impl_wp_ResourceRepository
     */
    private $_resourceRepository;

    public function __construct(tubepress_wordpress_impl_wp_ResourceRepository $resourceRepo)
    {
        $this->_resourceRepository = $resourceRepo;
    }

    public function onWpPostCategories(tubepress_api_event_EventInterface $event)
    {
        $terms   = $this->_resourceRepository->getAllCategories();
        $result = array();

        foreach ($terms as $term) {

            $result[$term->slug] = $term->name;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostTags(tubepress_api_event_EventInterface $event)
    {
        $terms   = $this->_resourceRepository->getAllTags();
        $result = array();

        foreach ($terms as $term) {

            $result[$term->slug] = $term->name;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostTemplate(tubepress_api_event_EventInterface $event)
    {
        $templates = $this->_resourceRepository->getPageTemplates();

        $this->_sortArrayAndSetAsSubject($templates, $event);
    }

    public function onWpPostType(tubepress_api_event_EventInterface $event)
    {
        $types  = $this->_resourceRepository->getAllUsablePostTypes();
        $result = array();

        foreach ($types as $type) {

            $result[$type->name] = $type->labels->singular_name;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpPostStatus(tubepress_api_event_EventInterface $event)
    {
        $result   = array();
        $statuses = $this->_resourceRepository->getAllUsablePostStatuses();

        foreach ($statuses as $status) {

            $result[$status->name] = $status->label;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    public function onWpUser(tubepress_api_event_EventInterface $event)
    {
        $result   = array();
        $authors = $this->_resourceRepository->getAuthors();

        foreach ($authors as $user) {

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