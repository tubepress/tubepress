<?php

defined('WP_PLUGIN_URL') || define('WP_PLUGIN_URL', 'fooby');

global $enqueuedStyles,
       $enqueuedScripts,
       $add_options_page_called,
       $registeredScripts,
       $registeredStyles,
       $isAdmin,
       $wp_register_sidebar_widget_called,
       $wp_register_widget_control;
       
$enqueuedStyles                    = array();
$enqueuedScripts                   = array();
$registeredStyles                  = array();
$registeredScripts                 = array();
$add_options_page_called           = false;
$isAdmin                           = false;
$wp_register_sidebar_widget_called = false;
$wp_register_widget_control        = false;

function is_admin()
{
    global $isAdmin;
    return $isAdmin;
}

function add_options_page($one, $two, $three, $four, $five)
{
    if ($one !== 'TubePress Options') {
        throw new Exception("add_options_page called with wrong first arg: $one");
    }
    if ($two !== 'TubePress') {
        throw new Exception("add_options_page called with wrong second arg: $two");
    }
    if ($three !== 'manage_options') {
        throw new Exception("add_options_page called with wrong third arg: $three");
    }
    if (strpos($four, 'classes/org/tubepress/impl/env/wordpress/Admin.class.php') === false) {
        throw new Exception("Bad file path: $four");
    }
    if (!is_array($five)) {
        throw new Exception("non-array passed to add_options_page");
    }
    if ($five[0] !== 'org_tubepress_impl_env_wordpress_Admin') {
        throw new Exception("Bad callback");
    }
    if ($five[1] !== 'conditionalExecuteOptionsPage') {
        throw new Exception('Bad callback');
    }
    global $add_options_page_called;
    $add_options_page_called = true;
}

if (!function_exists('get_option')) {
    function get_option($optionName) {
        return 'valueOf-' . $optionName;
    }
}

if (!function_exists('update_option')) {
    function update_option($optionName) {
        return "";
    }
}

if (!function_exists('get_alloptions')) {
    function get_alloptions() {
        return array('foo' => 'bar');
    }
}

function wp_enqueue_style($one) 
{
    global $enqueuedStyles;
    $enqueuedStyles[$one] = true;
}
        
function wp_enqueue_script($script)
{
    global $enqueuedScripts;
    $enqueuedScripts[$script] = true;
}

function add_option($name, $value)
{
    
}

function wp_register_style($name, $path)
{
    global $registeredStyles;
    $registeredStyles[$name] = $path;
}

function wp_register_script($name, $path)
{
    global $registeredScripts;
    $registeredScripts[$name] = $path;
}

if (!function_exists('__')) {
    function __($something)
    {
        return $something;
    }
}

if (!function_exists('load_plugin_textdomain')) {
    function load_plugin_textdomain() {}
}

if (!function_exists('add_filter')) {
    function add_filter() {}
}

if (!function_exists('add_action')) {
    function add_action() {}
}

function wp_register_sidebar_widget($one, $two, $three, $four)
{
    if ($one !== 'tubepress') {
        throw new Exception("bad first arg to wp_register_sidebar_widget");
    }
    if ($two !== 'TubePress') {
        throw new Exception('bad second arg to wp_register_sidebar_widget');
    }
    if (!is_array($three) || count($three) !== 2 || $three[0] !== 'org_tubepress_impl_env_wordpress_Widget' || $three[1] !== 'printWidget') {
        throw new Exception('bad third arg to wp_register_sidebar_widget');
    }
    if (!isset($four)) {
        throw new Exception('missing fourth arg to wp_register_sidebar_widget');
    }
    global $wp_register_sidebar_widget_called;
    $wp_register_sidebar_widget_called = true;
}

function wp_register_widget_control($one, $two, $three)
{
    if ($one !== 'tubepress') {
        throw new Exception("bad first arg to wp_register_sidebar_widget");
    }
    if ($two !== 'TubePress') {
        throw new Exception('bad second arg to wp_register_sidebar_widget');
    }
    if (!is_array($three) || count($three) !== 2 || $three[0] !== 'org_tubepress_impl_env_wordpress_Widget' || $three[1] !== 'printControlPanel') {
        throw new Exception('bad third arg to wp_register_sidebar_widget');
    }
    global $wp_register_widget_control;
    $wp_register_widget_control = true;
}
