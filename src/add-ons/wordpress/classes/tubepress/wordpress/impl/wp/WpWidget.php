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

class tubepress_wordpress_impl_wp_WpWidget extends WP_Widget
{
    private static $_TEMPLATE_VAR_TEXT_FIELD_DESC   = 'textFieldDesc';
    private static $_TEMPLATE_VAR_TEXT_FIELD_ID     = 'textFieldId';
    private static $_TEMPLATE_VAR_TEXT_FIELD_NAME   = 'textFieldName';
    private static $_TEMPLATE_VAR_TEXT_FIELD_VALUE  = 'textFieldValue';
    private static $_TEMPLATE_VAR_TITLE_FIELD_ID    = 'titleFieldId';
    private static $_TEMPLATE_VAR_TITLE_FIELD_NAME  = 'titleFieldName';
    private static $_TEMPLATE_VAR_TITLE_FIELD_VALUE = 'titleFieldValue';

    private static $_DEFAULT_WIDGET_OPTIONS = array(
        tubepress_api_options_Names::FEED_RESULTS_PER_PAGE    => 3,
        tubepress_api_options_Names::META_DISPLAY_VIEWS       => 'false',
        tubepress_api_options_Names::META_DISPLAY_DESCRIPTION => 'true',
        tubepress_api_options_Names::META_DESC_LIMIT          => 50,
        tubepress_api_options_Names::PLAYER_LOCATION          => 'shadowbox',
        tubepress_api_options_Names::GALLERY_THUMB_HEIGHT     => 105,
        tubepress_api_options_Names::GALLERY_THUMB_WIDTH      => 135,
        tubepress_api_options_Names::GALLERY_PAGINATE_ABOVE   => 'false',
        tubepress_api_options_Names::GALLERY_PAGINATE_BELOW   => 'false',
        tubepress_api_options_Names::THEME                    => 'tubepress/default',
        tubepress_api_options_Names::GALLERY_FLUID_THUMBS     => 'false',
    );

    public function __construct()
    {
        /* @noinspection PhpUndefinedClassInspection */
        /* @noinspection PhpUndefinedFunctionInspection */
        parent::__construct(
            'tubepress_widget_gattaca',
            'TubePress',
            array(
                "description" => __('Displays YouTube or Vimeo videos with TubePress', "tubepress"), //>(translatable)<
            )
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        $title = empty($instance['title']) ? '' : $instance['title'];
        $text  = empty($instance['text'])  ? '' : $instance['text'];

        /* @noinspection PhpUndefinedFunctionInspection */
        $title = apply_filters('widget_title', $title, $instance, 'tubepress_widget_gattaca');

        /* @noinspection PhpUndefinedFunctionInspection */
        $text = do_shortcode($text);

        echo $args['before_widget'];

        if (!empty($title)) {

            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo $text . $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $sc = $this->_getServiceContainer();

        /*
         * @var tubepress_api_template_TemplatingInterface
         */
        $templating = $sc->get(tubepress_api_template_TemplatingInterface::_ . '.admin');

        $defaults = array(
            'title' => 'TubePress',
            'text'  => $this->_getDefaultShortcode(),
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        $title    = strip_tags($instance['title']);
        $text     = esc_textarea($instance['text']);

        $templateVars = array(

            self::$_TEMPLATE_VAR_TITLE_FIELD_ID    => $this->get_field_id('title'),
            self::$_TEMPLATE_VAR_TITLE_FIELD_NAME  => $this->get_field_name('title'),
            self::$_TEMPLATE_VAR_TITLE_FIELD_VALUE => $title,

            self::$_TEMPLATE_VAR_TEXT_FIELD_ID    => $this->get_field_id('text'),
            self::$_TEMPLATE_VAR_TEXT_FIELD_NAME  => $this->get_field_name('text'),
            self::$_TEMPLATE_VAR_TEXT_FIELD_DESC  => __(sprintf('TubePress shortcode for the widget. See the <a href="%s" target="_blank">documentation</a>.', "http://docs.tubepress.com/"), "tubepress"), //>(translatable)<
            self::$_TEMPLATE_VAR_TEXT_FIELD_VALUE => $text,
        );

        echo $templating->renderTemplate('wordpress/modern-widget-controls', $templateVars);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);

        if (current_user_can('unfiltered_html')) {

            $instance['text'] = $new_instance['text'];

        } else {

            $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
        }

        return $instance;
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface
     */
    private function _getServiceContainer()
    {
        return require TUBEPRESS_ROOT . '/src/php/scripts/boot.php';
    }

    private function _getDefaultShortcode()
    {
        $toImplode = array();
        $sc        = $this->_getServiceContainer();

        /*
         * @var tubepress_api_options_ContextInterface
         */
        $context = $sc->get(tubepress_api_options_ContextInterface::_);

        $trigger = $context->get(tubepress_api_options_Names::SHORTCODE_KEYWORD);

        foreach (self::$_DEFAULT_WIDGET_OPTIONS as $name => $value) {

            $toImplode[] = $name . '="' . $value . '"';
        }

        return "[$trigger " . implode(' ', $toImplode) . ']';
    }
}
