<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_impl_wp_WpFunctions
{
    const _ = 'tubepress_wordpress_impl_wp_WpFunctions';

    /**
     * Retrieves the translated string from WordPress's translate().
     *
     * @param string $message Text to translate.
     * @param string $domain  Domain to retrieve the translated text.
     *
     * @return string Translated text.
     */
    public function __($message, $domain)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return $message == '' ? '' : __($message, $domain);
    }

    /**
     * Adds a hook for a shortcode tag.
     *
     * @param $tag      string   Shortcode tag to be searched in post content
     * @param $function callable Hook to run when shortcode is found
     *
     * @return void
     */
    public function add_shortcode($tag, $function)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        add_shortcode($tag, $function);
    }

    /**
     * Retrieve the name of the current filter or action.
     *
     * @return string Hook name of the current filter or action.
     */
    public function current_filter()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return current_filter();
    }

    /**
     * Use the function update_option() to update a named option/value pair to the options database table.
     * The option_name value is escaped with $wpdb->escape before the INSERT statement.
     *
     * @param string $name  Name of the option to update.
     * @param string $value The NEW value for this option name. This value can be a string, an array,
     *                      an object or a serialized value.
     *
     * @return bool True if option value has changed, false if not or if update failed.
     */
    public function update_option($name, $value)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return update_option($name, $value);
    }

    /**
     * Retrieve list of category objects.
     *
     * https://developer.wordpress.org/reference/functions/get_categories/
     *
     * @param array $args Change the defaults retrieving categories.
     *
     * @return array List of categories.
     */
    public function get_categories(array $args = array())
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_categories($args);
    }

    /**
     * Get the current locale.
     *
     * If the locale is set, then it will filter the locale in the 'locale' filter
     * hook and return the value.
     *
     * If the locale is not set already, then the WPLANG constant is used if it is
     * defined. Then it is filtered through the 'locale' filter hook and the value
     * for the locale global set and the locale is returned.
     *
     * The process to get the locale should only be done once, but the locale will
     * always be filtered using the 'locale' hook.
     *
     * @since 1.5.0
     *
     * @return string The locale of the blog or from the 'locale' hook.
     */
    public function get_locale()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_locale();
    }

    /**
     * A safe way of getting values for a named option from the options database table.
     *
     * @param string $name Name of the option to retrieve.
     *
     * @return mixed Mixed values for the option.
     */
    public function get_option($name)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_option($name);
    }

    /**
     * Returns an array of post status names or objects.
     *
     * https://codex.wordpress.org/Function_Reference/get_post_stati
     *
     * @param array  $args     Array of key => value pairs used to filter results.
     * @param string $output   Whether to output names or objects.
     * @param string $operator Whether to return statuses matching ALL ('and') or ANY ('or') arguments.
     *
     * @return array An array of post names or objects, depending on $output parameter.
     */
    public function get_post_stati(array $args = array(), $output = 'names', $operator = 'and')
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_post_stati($args, $output, $operator);
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Retrieve full permalink for current post or post ID.
     *
     * @param int|WP_Post $post      Post ID or post object. Default is the global $post.
     * @param bool        $leavename Whether to keep post name or page name.
     *
     * @return string|false The permalink URL or false if post does not exist.
     */
    public function get_permalink($post, $leavename = false)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_permalink($post, $leavename);
    }

    /**
     * Create an array of posts based on a set of parameters.
     *
     * @param array $args See http://codex.wordpress.org/Function_Reference/get_posts.
     *
     * @return array List of post objects.
     */
    public function get_posts($args)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_posts($args);
    }

    /**
     * Returns the registered post types as found in $wp_post_types.
     *
     * https://codex.wordpress.org/Function_Reference/get_post_types
     *
     * @param array  $args     An array of key value arguments to match against the post types.
     * @param string $output   The type of output to return, either 'names' or 'objects'.
     * @param string $operator Operator (and/or) to use with multiple $args.
     *
     * @return array A list of post names or objects.
     */
    public function get_post_types(array $args = array(), $output = 'names', $operator = 'and')
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_post_types($args, $output, $operator);
    }

    /**
     * Retrieve an array of objects for each term in post_tag taxonomy.
     *
     * https://codex.wordpress.org/Function_Reference/get_tags
     *
     * @param array $args
     *
     * @return array Returns either an array of objects or an empty array.
     */
    public function get_tags(array $args = array())
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_tags($args);
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Retrieve user info by a given field.
     *
     * https://developer.wordpress.org/reference/functions/get_user_by/
     *
     * @param string     $field The field to retrieve the user with. id | ID | slug | email | login.
     * @param int|string $value A value for $field. A user ID, slug, email address, or login name.
     *
     * @return WP_User|bool WP_User object on success, false on failure.
     */
    public function get_user_by($field, $value)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_user_by($field, $value);
    }

    /**
     * Retrieves an array of users matching the criteria given in $args.
     *
     * @param array $args See https://codex.wordpress.org/Function_Reference/get_users
     *
     * @return array An array of IDs, stdClass objects, or WP_User objects, depending on the value of the 'fields'
     *               parameter.
     */
    public function get_users(array $args = array())
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return get_users($args);
    }

    /**
     * Remove an enqueued script.
     *
     * @param string $handle Name of the script.
     *
     * @return void
     */
    public function wp_dequeue_script($handle)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_dequeue_script($handle);
    }

    /**
     * Remove a CSS file that was enqueued with wp_enqueue_style().
     *
     * @param string $handle Name of the enqueued stylesheet.
     *
     * @return void
     */
    public function wp_dequeue_style($handle)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_dequeue_style($handle);
    }

    /**
     * Remove a registered script (javascript).
     *
     * @param string $handle Name of the script.
     *
     * @return void
     */
    public function wp_deregister_script($handle)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_deregister_script($handle);
    }

    /**
     * A safe way of adding a named option/value pair to the options database table. It does nothing if the option already exists.
     *
     * @param string $name  Name of the option to be added. Must not exceed 64 characters. Use underscores to separate
     *                      words, and do not use uppercase—this is going to be placed into the database.
     * @param string $value Value for this option name.
     *
     * @return void
     */
    public function add_option($name, $value)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        add_option($name, $value);
    }

    /**
     * Add a sub menu page.
     *
     * @param string   $parent_slug The slug name for the parent menu (or the file name of a standard WordPress
     *                              admin page). Set to 'options.php' if you want to create a page that doesn't appear
     *                              in any menu
     * @param string   $page_title  The text to be displayed in the title tags of the page when the menu is selected
     * @param string   $menu_title  The text to be used for the menu
     * @param string   $capability  The capability required for this menu to be displayed to the user.
     * @param string   $menu_slug   The slug name to refer to this menu by (should be unique for this menu). If you want
     *                              to NOT duplicate the parent menu item, you need to set the name of the $menu_slug
     *                              exactly the same as the parent slug.
     * @param callback $function    The function to be called to output the content for this page.
     *
     * @return string The resulting page's hook_suffix, or false if the user does not have the capability required...
     */
    public function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
    }

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
    public function add_options_page($pageTitle, $menuTitle, $capability, $menu_slug, $callback)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return add_options_page($pageTitle, $menuTitle, $capability, $menu_slug, $callback);
    }

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
    public function check_admin_referer($action, $queryArg)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return check_admin_referer($action, $queryArg);
    }

    /**
     * This Conditional Tag checks if the Dashboard or the administration panel is being displayed.
     *
     * @return bool True on success, otherwise false.
     */
    public function is_admin()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return is_admin();
    }

    /**
     * The plugins_url template tag retrieves the url to the addons directory or to a specific file within that directory.
     *
     * @param string $path   Path relative to the addons URL.
     * @param string $plugin The plugin file that you want to be relative to.
     *
     * @return string addons url link with optional path appended.
     */
    public function plugins_url($path, $plugin)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return plugins_url($path, $plugin);
    }

    /**
     * Gets the basename of a plugin (extracts the name of a plugin from its filename).
     *
     * @param string $file The filename of a plugin.
     *
     * @return string The basename of the plugin.
     */
    public function plugin_basename($file)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return plugin_basename($file);
    }

    /**
     * The safe and recommended method of adding JavaScript to a WordPress generated page.
     *
     * @param string $handle    Name of the script.
     * @param string $src       URL to the script, e.g. http://example.com/wp-content/themes/my-theme/my-theme-script.js.
     * @param array  $deps      Array of the handles of all the registered scripts that this script depends on,
     *                          that is the scripts that must be loaded before this script.
     * @param string $ver       String specifying the script version number, if it has one,
     *                          which is concatenated to the end of the path as a query string.
     * @param bool   $in_footer If this parameter is true, the script is placed before the </body> end tag.
     *
     * @return void
     */
    public function wp_enqueue_script($handle, $src, $deps, $ver, $in_footer)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
    }

    /**
     * A safe way to add/enqueue a CSS style file to the wordpress generated page.
     *
     * @param string $handle Name of the stylesheet.
     *
     * @return void
     */
    public function wp_enqueue_style($handle)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_enqueue_style($handle);
    }

    /**
     * A safe way of regisetring javascripts in WordPress for later use with wp_enqueue_script().
     *
     * @param string $handle   Name of the script.
     * @param string $src      URL to the script.
     * @param array  $deps     Array of the handles of all the registered scripts that this script depends on.
     * @param string $version  String specifying the script version number
     * @param bool   $inFooter If this parameter is true the script is placed at the bottom of the <body>
     *
     * @return void
     */
    public function wp_register_script($handle, $src, $deps = array(), $version = null, $inFooter = false)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_register_script($handle, $src, $deps, $version, $inFooter);
    }

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
    public function wp_register_sidebar_widget($id, $name, $callback, $options)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_register_sidebar_widget($id, $name, $callback, $options);
    }

    /**
     * A safe way to register a CSS style file for later use with wp_enqueue_style().
     *
     * @param string $handle Name of the stylesheet (which should be unique as it is used to identify the script in the whole system.
     * @param string $src    URL to the stylesheet.     *
     *
     * @return void
     */
    public function wp_register_style($handle, $src, $deps = array(), $version = null)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_register_style($handle, $src, $deps, $version);
    }

    /**
     * Registers widget control callback for customizing options.
     *
     * @param string $id       Sidebar ID.
     * @param string $name     Sidebar display name.
     * @param mixed  $callback Runs when the sidebar is displayed.
     *
     * @return void
     */
    public function wp_register_widget_control($id, $name, $callback)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        wp_register_widget_control($id, $name, $callback);
    }

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
    public function add_action($tag, $function, $priority, $acceptedArgs)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        add_action($tag, $function, $priority, $acceptedArgs);
    }

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
     *
     * @return void
     */
    public function add_filter($tag, $function, $priority, $acceptedArgs)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        add_filter($tag, $function, $priority, $acceptedArgs);
    }

    /**
     * Checks if SSL is being used.
     *
     * @return bool True if SSL, false otherwise.
     */
    public function is_ssl()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return is_ssl();
    }

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
    public function load_plugin_textdomain($domain, $absPath, $relPath)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        load_plugin_textdomain($domain, $absPath, $relPath);
    }

    /**
     * The site_url template tag retrieves the site url for the current site (where the WordPress core files reside)
     * with the appropriate protocol, 'https' if is_ssl() and 'http' otherwise.
     * If scheme is 'http' or 'https', is_ssl() is overridden.
     *
     * @return string The site URL link.
     */
    public function site_url()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return site_url();
    }

    /**
     * The content_url template tag retrieves the url to the content area for the current site with the
     * appropriate protocol, 'https' if is_ssl() and 'http' otherwise.
     *
     * @param string $path Path relative to the content url.
     *
     * @return string Content url link with optional path appended.
     */
    public function content_url($path = '')
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return content_url($path);
    }

    /**
     * @return string The current WP version.
     */
    public function wp_version()
    {
        global $wp_version;

        return $wp_version;
    }

    /**
     * The register_activation_hook function registers a plugin function to be run when the plugin is activated.
     *
     * @param string   $file     Path to the main plugin file inside the wp-content/plugins directory. A full path will work.
     * @param callback $function The function to be run when the plugin is activated. Any of PHP's callback pseudo-types will work.
     */
    public function register_activation_hook($file, $function)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return register_activation_hook($file, $function);
    }

    /**
     * Retrieves or displays the nonce hidden form field.
     *
     * @param string $action   Action name. Should give the context to what is taking place. Optional but recommended.
     * @param string $name     Nonce name. This is the name of the nonce hidden form field to be created.
     *                         Once the form is submitted, you can access the generated nonce via $_POST[$name].
     * @param bool   $referrer Whether also the referer hidden form field should be created with the wp_referer_field()
     * @param bool   $echo     Whether to display or return the nonce hidden form field, and also the referer hidden form field if the $referer argument is set to true.
     *
     * @return mixed
     */
    public function wp_nonce_field($action, $name, $referrer, $echo)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_nonce_field($action, $name, $referrer, $echo);
    }

    /**
     * Verify that a nonce is correct and unexpired with the respect to a specified action.
     *
     * @param string $nonce  Nonce to verify.
     * @param string $action Action name. Should give the context to what is taking place and be the same when the nonce was created.
     *
     * @return bool|int False if the nonce is invalid. Otherwise returns an integer with the value of
     *                  1 if the nonce has been generated in the past 12 hours or less.
     *                  2 if the nonce was generated between 12 and 24 hours ago.
     */
    public function wp_verify_nonce($nonce, $action)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_verify_nonce($nonce, $action);
    }

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
    public function admin_url($path = null, $scheme = 'admin')
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return admin_url($path, $scheme);
    }

    /**
     * Determine whether the current user has a certain capability.
     *
     * @param $capability string A capability. This is case-sensitive, and should be all lowercase.
     * @param $args       mixed  Any additional arguments that may be needed, such as a post ID.
     *                           Some capability checks (like 'edit_post' or 'delete_page') require this be provided.
     *
     * @return mixed
     */
    public function current_user_can($capability, $args = null)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return current_user_can($capability, $args);
    }

    /**
     * Generates and returns a nonce. The nonce is generated based on the current time, the $action argument, and
     * the current user ID.
     *
     * @param $action string Action name. Should give the context to what is taking place. Optional but recommended.
     *
     * @return string The one use form token.
     */
    public function wp_create_nonce($action = null)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_create_nonce($action);
    }

    /**
     * Insert or update a post.
     *
     * @param array $postArray An array of elements that make up a post to update or insert.
     * @param bool  $wpError   Whether to allow return of WP_Error on failure
     */
    public function wp_insert_post(array $postArray, $wpError = false)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_insert_post($postArray, $wpError);
    }

    /**
     * @return array List of all options.
     */
    public function wp_load_alloptions()
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_load_alloptions();
    }

    /**
     * Registers a widget.
     *
     * @param string $class
     *
     * @return void
     */
    public function register_widget($class)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        register_widget($class);
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * @return WP_Scripts
     */
    public function wp_scripts() {

        global $wp_scripts;

        /* @noinspection PhpUndefinedClassInspection */
        if (!($wp_scripts instanceof WP_Scripts)) {

            /* @noinspection PhpUndefinedClassInspection */
            $wp_scripts = new WP_Scripts();
        }

        return $wp_scripts;
    }

    /** @noinspection PhpUndefinedClassInspection */
    /**
     * Gets a WP_Theme object for a theme.
     *
     * @param string $stylesheet Directory name for the theme. Defaults to current theme.
     * @param string $theme_root Absolute path of the theme root to look in. If not specified, the value returned by
     *                           get_raw_theme_root() will be used.
     *
     * @return WP_Theme
     */
    public function wp_get_theme($stylesheet = null, $theme_root = null)
    {
        /* @noinspection PhpUndefinedFunctionInspection */
        return wp_get_theme($stylesheet, $theme_root);
    }
}
