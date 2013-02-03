<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

interface tubepress_plugins_wordpress_spi_WordPressFunctionWrapper
{
    const _ = 'tubepress_plugins_wordpress_spi_WordPressFunctionWrapper';

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
     * @param string $relPath Relative path to WP_PLUGIN_DIR, with a trailing slash. This is the preferred argument to use.
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
     * The plugins_url template tag retrieves the url to the plugins directory or to a specific file within that directory.
     *
     * @param string $path   Path relative to the plugins URL.
     * @param string $plugin The plugin file that you want to be relative to.
     *
     * @return string Plugins url link with optional path appended.
     */
    function plugins_url($path, $path);

    /**
     * The site_url template tag retrieves the site url for the current site (where the WordPress core files reside)
     * with the appropriate protocol, 'https' if is_ssl() and 'http' otherwise.
     * If scheme is 'http' or 'https', is_ssl() is overridden.
     *
     * @return string The site URL link.
     */
    function site_url();

    /**
     * The safe and recommended method of adding JavaScript to a WordPress generated page.
     *
     * @param string $handle Name of the script.
     *
     * @return void
     */
    function wp_enqueue_script($handle);

    /**
     * A safe way to add/enqueue a CSS style file to the wordpress generated page.
     *
     * @param string $handle Name of the stylesheet.
     *
     * @return void
     */
    function wp_enqueue_style($handle);

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