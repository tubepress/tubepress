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
        $terms = $this->_resourceRepository->getAllCategories();

        $this->_handleIncomingTerms($terms, $event, 'categories');
    }

    public function onWpPostTags(tubepress_api_event_EventInterface $event)
    {
        $terms = $this->_resourceRepository->getAllTags();

        $this->_handleIncomingTerms($terms, $event, 'tags');
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
        $result  = array();
        $authors = $this->_resourceRepository->getAuthors();

        foreach ($authors as $user) {

            $loginName = $this->_deIntegerizeLoginName($user->user_login);
            $display   = $user->display_name;

            $result[$loginName] = $display;
        }

        $this->_sortArrayAndSetAsSubject($result, $event);
    }

    /**
     * Some WP users have names that are integers. This messes up TubePress's validation so
     * we prepend those names with a prefix.
     */
    private function _deIntegerizeLoginName($incoming)
    {
        if ((string) intval($incoming) === "$incoming") {

            return "tubepress_wp_user_$incoming";
        }

        return $incoming;
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

    private function _handleIncomingTerms(array $registeredTerms, tubepress_api_event_EventInterface $event, $name)
    {
        $optionValue = $event->getArgument('optionValue');

        if (!$optionValue) {

            //no tags or categories requested
            return;
        }

        $exploded = preg_split('~\s*,\s*~', $optionValue);

        if (!is_array($exploded)) {

            //this should never happen, right?
            return;
        }

        $toKeep = array();

        foreach ($exploded as $incomingTerm) {

            foreach ($registeredTerms as $registeredTerm) {

                if ($registeredTerm->slug === $incomingTerm) {

                    $toKeep[] = $incomingTerm;
                    break;
                }
            }
        }

        if (count($toKeep) === 0) {

            $newValue = null;

        } else {

            $toKeep   = array_unique($toKeep);
            $newValue = implode(',', $toKeep);
        }

        $event->setArgument('optionValue', $newValue);
    }
}
