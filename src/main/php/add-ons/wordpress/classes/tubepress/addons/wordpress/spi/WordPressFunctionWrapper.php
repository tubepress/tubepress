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

interface tubepress_addons_wordpress_spi_WordPressFunctionWrapper
{
    const _ = 'tubepress_addons_wordpress_spi_WordPressFunctionWrapper';

    /**
     * Retrieves the translated string from WordPress's translate().
     *
     * @param string $message Text to translate.
     * @param string $domain  Domain to retrieve the translated text.
     *
     * @return string Translated text.
     */
    function __($message, $domain);

    /**
     * Hooks a function on to a specific action.
     *
     * @param string $tag          The name of the action to which $function_to_add is hooked.
     * @param mixed  $function     The name of the function you wish to be called.
     * @param int    $priority     Used to specify the order in which the functions associated with a particular
     *                             action are executed. Lower numbers correspond with earlier execution, and
     *                             functions with the same priority are executed in the order in which they were
     *                             added to the action.
     * @param int    $acceptedArgs The number of arguments the function accepts.
     *
     * @return void
     */
    function add_action($tag, $function, $priority, $acceptedArgs);

    /**
     * Hooks a function to a specific filter action.
     *
     * @param string $tag          The name of the filter to hook the $function_to_add to.
     * @param mixed  $function     A callback for the function to be called when the filter is applied.
     * @param int    $priority     Used to specify the order in which the functions associated with a particular
     *                             filter are executed. Lower numbers correspond with earlier execution, and
     *                             functions with the same priority are executed in the order in which they were
     *                             added to the action.
     * @param int    $acceptedArgs The number of arguments the function accepts.
     * @return void
     */
    function add_filter($tag, $function, $priority, $acceptedArgs);

    /**
     * A safe way of adding a named option/value pair to the options database table. It does nothing if the option already exists.
     *
     * @param string $name  Name of the option to be added. Use underscores to separate words, and do not
     *                      use uppercaseâ€”this is going to be placed into the database.
     * @param string $value Value for this option name.
     *
     * @return void
     */
    function add_option($name, $value);

    /**
     * Add sub menu page to the Settings menu.
     *
     * @param string $pageTitle  The text to be displayed in the title tags of the page when the menu is selected.
     * @param string $menuTitle  The text to be used for the menu
     * @param string $capability The capability required for this menu to be displayed to the user.
     * @param string $menu_slug  The slug name to refer to this menu by (should be unique for this menu).
     * @param mixed  $callback   The function to be called to output the content for this page.
     *
     * @return mixed
     */
    function add_options_page($pageTitle, $menuTitle, $capability, $menu_slug, $callback);

    /**
     * The admin_url template tag retrieves the url to the admin area for the current site with the appropriate
     * protocol, 'https' if is_ssl() and 'http' otherwise. If scheme is 'http' or 'https', is_ssl() is overridden.
     *
     * @param string $path   Path relative to the admin url.
     * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http'
     *                       or 'https' can be passed to force those schemes. The function uses get_site_url(), so
     *                       allowed values include any accepted by that function.
     *
     * @return string Admin url link with optional path appended.
     */
    function admin_url($path = null, $scheme = 'admin');

    /**
     * Tests if the current request was referred from an admin page, or (given $action parameter)
     * if the current request carries a valid nonce. Used to avoid security exploits.
     *
     * @param string $action   Action nonce.
     * @param string $queryArg Where to look for nonce in $_REQUEST
     *
     * @return mixed Function dies with an appropriate message ("Are you sure you want to do this?" is the default)
     *               if not referred from admin page, returns boolean true if the admin referer was was successfully validated.
     */
    function check_admin_referer($action, $queryArg);

    /**
     * The content_url template tag retrieves the url to the content area for the current site with the
     * appropriate protocol, 'https' if is_ssl() and 'http' otherwise.
     *
     * @return string Content url link with optional path appended.
     */
    function content_url();

    /**
     * A safe way of removing a named option/value pair from the options database table.
     *
     * @param string $name Name of the option to be deleted.
     *
     * @return boolean TRUE if the option has been successfully deleted, otherwise FALSE.
     */
    function delete_option($name);

    /**
     * A safe way of getting values for a named option from the options database table.
     *
     * @param string $name Name of the option to retrieve.
     *
     * @return mixed Mixed values for the option.
     */
    function get_option($name);

    /**
     * Returns an array of pages that are in the blog, optionally constrained by parameters.
     * This array is not tree-like (hierarchical).
     *
     * @param array $args See http://codex.wordpress.org/Function_Reference/get_pages.
     *
     * @return array An array containing all the Pages matching the request. The returned array is an array of "page" objects.
     *               See http://codex.wordpress.org/Function_Reference/get_pages.
     */
    function get_pages($args);

    /**
     * Returns the available page templates in the currently active theme.
     * It searches all the current theme's template files for the commented Template Name: name of template.
     *
     * @return array Where key is the filename and value is the name of the template.
     */
    function get_page_templates();

    /**
     * Create an array of posts based on a set of parameters.
     *
     * @param array $args See http://codex.wordpress.org/Function_Reference/get_posts.
     *
     * @return array List of post objects.
     */
    function get_posts($args);

    /**
     * Retrieve the numeric ID of the current post. This tag must be within The Loop.
     *
     * @return integer The ID of the current post.
     */
    function get_the_ID();

    /**
     * @return string The current WP version.
     */
    function wp_version();

    /**
     * This Conditional Tag checks if the Dashboard or the administration panel is being displayed.
     *
     * @return boolean True on success, otherwise false.
     */
    function is_admin();

    /**
     * Checks if SSL is being used.
     *
     * @return boolean True if SSL, false otherwise.
     */
    function is_ssl();

    /**
     * Loads the plugin's translated strings.
     *
     * @param string $domain  Unique identifier for retrieving translated strings.
     * @param string $absPath Relative path to ABSPATH of a folder, where the .mo file resides. Deprecated, but still functional until 2.7.
     * @param string $relPath Relative path to WP_addon_DIR, with a trailing slash. This is the preferred argument to use.
     *                        It takes precendence over $abs_rel_path
     *
     * @return void
     */
    function load_plugin_textdomain($domain, $absPath, $relPath);

    /**
     * Gets the basename of a plugin (extracts the name of a plugin from its filename).
     *
     * @param string $file The filename of a plugin.
     *
     * @return string The basename of the plugin.
     */
    function plugin_basename($file);

    /**
     * The plugins_url template tag retrieves the url to the addons directory or to a specific file within that directory.
     *
     * @param string $path   Path relative to the addons URL.
     * @param string $plugin The plugin file that you want to be relative to.
     *
     * @return string addons url link with optional path appended.
     */
    function plugins_url($path, $path);

    /**
     * The register_activation_hook function registers a plugin function to be run when the plugin is activated.
     *
     * @param string   $file     Path to the main plugin file inside the wp-content/plugins directory. A full path will work.
     * @param callback $function The function to be run when the plugin is activated. Any of PHP's callback pseudo-types will work.
     */
    function register_activation_hook($file, $function);

    /**
     * The site_url template tag retrieves the site url for the current site (where the WordPress core files reside)
     * with the appropriate protocol, 'https' if is_ssl() and 'http' otherwise.
     * If scheme is 'http' or 'https', is_ssl() is overridden.
     *
     * @return string The site URL link.
     */
    function site_url();

    /**
     * Remove an enqueued script.
     *
     * @param string $handle Name of the script.
     *
     * @return void
     */
    function wp_dequeue_script($handle);

    /**
     * Remove a CSS file that was enqueued with wp_enqueue_style().
     *
     * @param string $handle Name of the enqueued stylesheet.
     *
     * @return void
     */
    function wp_dequeue_style($handle);

    /**
     * Remove a registered script (javascript).
     *
     * @param string $handle Name of the script.
     *
     * @return void
     */
    function wp_deregister_script($handle);

    /**
     * The safe and recommended method of adding JavaScript to a WordPress generated page.
     *
     * @param string $handle     Name of the script.
     * @param string $src        URL to the script, e.g. http://example.com/wp-content/themes/my-theme/my-theme-script.js.
     * @param array  $deps       Array of the handles of all the registered scripts that this script depends on,
     *                           that is the scripts that must be loaded before this script.
     * @param string $ver        String specifying the script version number, if it has one,
     *                           which is concatenated to the end of the path as a query string.
     * @param boolean $in_footer If this parameter is true, the script is placed before the </body> end tag.
     *
     * @return void
     */
    function wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);

    /**
     * A safe way to add/enqueue a CSS style file to the wordpress generated page.
     *
     * @param string $handle Name of the stylesheet.
     *
     * @return void
     */
    function wp_enqueue_style($handle);

    /**
     * Localizes a script, but only if script has already been added. Can also be used to include arbitrary Javascript data in a page.
     *
     * @param string $handle     The script handle you are attaching the data for.
     * @param string $objectName The name for the Javascript object which will contain the data. Note that this should
     *                           be unique to both the script and to the plugin or theme. Thus, the value here should
     *                           be properly prefixed with the slug or another unique value, to prevent conflicts.
     *                           However, as this is a Javascript object name, it cannot contain dashes.
     *                           Use underscores or camelCasing.
     * @param array $l10n        The data itself. The data can be either a single or multi (as of 3.3) dimensional array.
     *
     * @return void
     */
    function wp_localize_script($handle, $objectName, array $l10n);

    /**
     * Retrieves or displays the nonce hidden form field.
     *
     * @param string  $action   Action name. Should give the context to what is taking place. Optional but recommended.
     * @param string  $name     Nonce name. This is the name of the nonce hidden form field to be created.
     *                                      Once the form is submitted, you can access the generated nonce via $_POST[$name].
     * @param boolean $referrer Whether also the referer hidden form field should be created with the wp_referer_field()
     * @param boolean $echo     Whether to display or return the nonce hidden form field, and also the referer hidden form field if the $referer argument is set to true.
     *
     * @return string The nonce hidden form field, optionally followed by the referer hidden form field if the $referer argument is set to true.
     */
    function wp_nonce_field($action, $name, $referrer, $echo);

    /**
     * A safe way of regisetring javascripts in WordPress for later use with wp_enqueue_script().
     *
     * @param string $handle Name of the script.
     * @param string $src    URL to the script.
     *
     * @return void
     */
    function wp_register_script($handle, $src);

    /**
     * Register WordPress Widgets for use in your themes sidebars.
     *
     * @param string $id       Widget ID.
     * @param string $name     Widget display title.
     * @param mixed  $callback Run when widget is called.
     * @param array  $options  Widget options.
     *
     * @return void
     */
    function wp_register_sidebar_widget($id, $name, $callback, $options);

    /**
     * A safe way to register a CSS style file for later use with wp_enqueue_style().
     *
     * @param string $handle Name of the stylesheet (which should be unique as it is used to identify the script in the whole system.
     * @param string $src    URL to the stylesheet.     *
     * @return void
     */
    function wp_register_style($handle, $src);

    /**
     * Registers widget control callback for customizing options.
     *
     * @param string $id       Sidebar ID.
     * @param string $name     Sidebar display name.
     * @param mixed  $callback Runs when the sidebar is displayed.
     *
     * @return void
     */
    function wp_register_widget_control($id, $name, $callback);

    /**
     * Verify that a nonce is correct and unexpired with the respect to a specified action.
     *
     * @param string $nonce  Nonce to verify.
     * @param string $action Action name. Should give the context to what is taking place and be the same when the nonce was created.
     *
     * @return boolean|integer False if the nonce is invalid. Otherwise returns an integer with the value of
     *                         1 if the nonce has been generated in the past 12 hours or less.
     *                         2 if the nonce was generated between 12 and 24 hours ago.
     */
    function wp_verify_nonce($nonce, $action);

    /**
     * Use the function update_option() to update a named option/value pair to the options database table.
     * The option_name value is escaped with $wpdb->escape before the INSERT statement.
     *
     * @param string $name  Name of the option to update.
     * @param string $value The NEW value for this option name. This value can be a string, an array,
     *                      an object or a serialized value.
     *
     * @return boolean True if option value has changed, false if not or if update failed.
     */
    function update_option($name, $value);
}