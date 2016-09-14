<?php
/**
 * Plugin Name:         EJO Client
 * Plugin URI:          https://github.com/erikjoling/ejo-client
 * Description:         Improved permissions and user experience for EJOweb clients.
 * Version:             1.3.1
 * Author:              Erik Joling
 * Author URI:          https://www.ejoweb.nl/
 * Text Domain:         ejo-client
 * Domain Path:         /languages
 *
 * GitHub Plugin URI:   https://github.com/erikjoling/ejo-client
 * GitHub Branch:       master
 *
 * Minimum PHP version: 5.3.0
 */

/**
 *
 */
final class EJO_Client
{
    /* Holds the instance of this class. */
    private static $_instance = null;

    /* Version number of this plugin */
    public static $version = '1.3.1';

    /* Stores the handle of this plugin */
    public static $handle;

    /* Stores the plugin sub-directory/file */
    public static $plugin;

    /* Stores the directory path for this plugin. */
    public static $dir;

    /* Stores the directory URI for this plugin. */
    public static $uri;

    /* Name of client role */
    public static $role_name = 'client';

    /* Returns the instance. */
    public static function init() 
    {
        if ( !self::$_instance )
            self::$_instance = new self;
        return self::$_instance;
    }

    /* Plugin setup. */
    private function __construct() 
    {
        /* Setup */
        self::setup();

        /* Add activation hook */
        register_activation_hook( __FILE__, array( 'EJO_Client', 'on_plugin_activation') );

        /* Add uninstall hook */
        register_uninstall_hook( __FILE__, array( 'EJO_Client', 'on_plugin_uninstall') );
        register_deactivation_hook( __FILE__, array( 'EJO_Client', 'on_plugin_uninstall') );

        //* Add Reset when a plugin has been (de)activated
        add_action( 'admin_init', array( 'EJO_Client', 'reset_on_every_plugin_activation'), 99 );

        //* Add Reset link to plugin actions row
        add_filter( 'plugin_action_links_' . self::$plugin, array( 'EJO_Client', 'add_plugin_actions_link' ) );

        //* Hook client-cap reset to plugin page
        add_action( 'pre_current_active_plugins', array( 'EJO_Client', 'reset_on_plugins_page' ) );
    }

    /* Defines the directory path and URI for the plugin. */
    public static function setup() 
    {
        self::$handle = dirname( plugin_basename( __FILE__ ) );
        self::$plugin = plugin_basename( __FILE__ );
        self::$dir = plugin_dir_path( __FILE__ );
        self::$uri = plugin_dir_url( __FILE__ );

        /* Load the translation for the plugin */
        load_plugin_textdomain(self::$handle, false, self::$handle . '/languages' );
    }


    /* Fire when activating this plugin */
    public static function on_plugin_activation()
    {
        self::register_client_role();
        self::set_client_caps();
    }

    /* Fire when uninstalling this plugin */
    public static function on_plugin_uninstall()
    {
        self::unregister_client_role();
    }   

    /* Register client role */
    public static function register_client_role() 
    {
        /* Try to get client role */
        $client_role = get_role( self::$role_name );

        /** 
         * If client-role doesn't exist, add it
         * Else remove capabilities of the existing client role
         */
        if ( is_null( $client_role ) ) {
            add_role( self::$role_name, __( 'Client' ) );
        }
        else {
            self::remove_client_caps($client_role);
        }
    }

    /* Reset Client Caps */
    public static function reset_client_caps( $client_role = null )
    {
        if (!$client_role)
            $client_role = get_role( self::$role_name );

        if ( is_null( $client_role ) ) {
            return __('No Client Role found');
        }

        //* Remove all current capabilities of the client-role
        self::remove_client_caps($client_role);

        //* Set the right caps for the client role
        self::set_client_caps($client_role);        
    }

    //* Set the right caps for the client role
    public static function set_client_caps( $client_role = null )
    {
        if (!$client_role)
            $client_role = get_role( self::$role_name );

        if ( is_null( $client_role ) ) {
            return __('No Client Role found');
        }

        //* Remove all current capabilities of the client-role
        self::remove_client_caps($client_role);

        //* Get default client caps
        $client_caps = self::get_default_client_caps();

        //* Add other capabilities    
        $client_caps = array_merge( $client_caps, self::get_blog_caps() ); // Blog
        $client_caps = array_merge( $client_caps, self::get_gravityforms_caps() ); // Gravity Forms
        $client_caps = array_merge( $client_caps, self::get_ejo_contactadvertentie_caps() ); // EJO Contactadvertenties

        //* Remove double capabilities
        $client_caps = array_unique($client_caps);

        //* Allow client_caps to be filtered
        $client_caps = apply_filters( 'ejo_client_caps', $client_caps );

        //* Add client capabilities to role
        foreach ($client_caps as $cap) {

            $client_role->add_cap( $cap );
        }
    }

    /* Get default caps for client */
    public static function get_default_client_caps() 
    {
        $default_client_caps = array(/* Get the right caps for gravityforms */
            //* Super Admin
            // 'manage_network',
            // 'manage_sites',
            // 'manage_network_users',
            // 'manage_network_plugins',
            // 'manage_network_themes',
            // 'manage_network_options',

            //* Admin
            // 'activate_plugins',
            // 'create_users',
            // 'delete_plugins',
            // 'delete_themes',
            // 'delete_users',
            // 'edit_files',
            // 'edit_plugins',
            'edit_theme_options',
            // 'edit_themes',
            // 'edit_users',
            'export',
            // 'import',
            // 'install_plugins',
            // 'install_themes',
            // 'list_users',
            // 'manage_options',
            // 'promote_users',
            // 'remove_users',
            // 'switch_themes',
            // 'update_core',
            // 'update_plugins',
            // 'update_themes',
            // 'edit_dashboard',

            //* Editor
            'moderate_comments',
            'manage_categories',
            'manage_links',
            'edit_others_posts',
            'edit_pages',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',
            'delete_others_posts',
            'delete_private_posts',
            'edit_private_posts',
            'read_private_posts',
            'delete_private_pages',
            'edit_private_pages',
            'read_private_pages',
            'unfiltered_html',

            //* Author
            'edit_published_posts',
            'upload_files',
            'publish_posts',
            'delete_published_posts',

            //* Contributor
            'edit_posts',
            'delete_posts',

            //* All
            'read',
        );

        //* Remove blog caps from client_caps by default
        $default_client_caps = array_diff($default_client_caps, self::get_blog_caps(true));

        return apply_filters( 'ejo_client_default_caps', $default_client_caps );
    }

    /* Get Blog capabilities */
    public static function get_blog_caps( $force_return = false ) 
    {
        //* Check if blog is enabled
        $is_blog_enabled = apply_filters( 'ejo_client_blog_enabled', true );

        //* Return empty array if blog is disabled and no forced return (check default_client_caps)
        if ( !$is_blog_enabled && !$force_return )
            return array();

        //* Blog capabilities
        $blog_caps = array(
            'edit_posts',
            'edit_others_posts',
            'edit_published_posts',
            'publish_posts',
            'delete_posts',
            'delete_others_posts',
            'delete_published_posts',
            'delete_private_posts',
            'edit_private_posts',
            'read_private_posts',
            'manage_categories',
            'moderate_comments',
        );

        return apply_filters( 'ejo_client_blog_caps', $blog_caps );
    }

    /* Get gravityforms caps */
    public static function get_gravityforms_caps() 
    {
        //* Filter if Gravity Forms should be included even if plugin is installed
        $is_gravityforms_enabled = apply_filters( 'ejo_client_gravityforms_enabled', true );

        if ( ! class_exists( 'GFForms' ) || ! $is_gravityforms_enabled ) 
            return array();

        $gravityforms_caps = array(
            'gravityforms_edit_forms',
            'gravityforms_delete_forms',
            'gravityforms_create_form',
            'gravityforms_view_entries',
            'gravityforms_edit_entries',
            'gravityforms_delete_entries',
            // 'gravityforms_view_settings',
            // 'gravityforms_edit_settings',
            // 'gravityforms_export_entries',
            // 'gravityforms_uninstall',
            'gravityforms_view_entry_notes',
            'gravityforms_edit_entry_notes',
            // 'gravityforms_view_updates',
            // 'gravityforms_view_addons',
            // 'gravityforms_preview_forms',
        );

        return apply_filters( 'ejo_client_gravityforms_caps', $gravityforms_caps );
    }

    /* Get EJO contactadverntie capabilities */
    public static function get_ejo_contactadvertentie_caps() 
    {
        //* Filter if EJO Contactads should be included even if plugin is installed
        $is_ejo_contactadvertenties_enabled = apply_filters( 'ejo_client_ejo-contactadvertenties_enabled', true );

        if ( ! class_exists( 'EJO_Contactads' )  || ! $is_ejo_contactadvertenties_enabled ) 
            return array();

        return apply_filters( 'ejo_client_ejo-contactadvertenties_caps', EJO_Contactads::get_caps() );
    }

    //* Check whether client-role has caps
    public static function get_client_caps()
    {
        $client_role = get_role( self::$role_name );

        return $client_role->capabilities;
    }

    //* Check whether client-role has caps
    public static function client_has_caps( $client_role = null )
    {
        if (!$client_role)
            $client_role = get_role( self::$role_name );

        //* Return true if not empty
        if ( ! empty($client_role->capabilities) )
            return true;

        return false;
    }

    //* Remove caps of the client-role
    public static function remove_client_caps( $client_role = null )
    {
        if (!$client_role)
            $client_role = get_role( self::$role_name );

        //* Remove capabilities
        foreach ($client_role->capabilities as $cap => $status ) {
            $client_role->remove_cap( $cap );
        }
    }

    /* Unregister client role */
    public static function unregister_client_role()
    {
        /* Remove client role */
        remove_role( self::$role_name );

        /** 
         * When a role is removed, the users who have this role lose all rights on the site 
         * Maybe assign them to editor role
         */
    }

    /* Add reset link to plugin actions row */
    public static function add_plugin_actions_link( $links )
    {
        $links[] = '<a href="'. esc_url( get_admin_url(null, 'plugins.php?reset-ejo-client=true') ) .'">Reset</a>';

        return $links;
    }

    /**
     * Reset client caps on every plugin (de)activation
     */
    public static function reset_on_every_plugin_activation()
    {
        global $pagenow;

        if ($pagenow == 'plugins.php') {

            if ( isset($_GET['activate']) || isset($_GET['deactivate']) || isset($_GET['activate-multi']) || isset($_GET['deactivate-multi']) ) {
                self::set_client_caps();
            }
        }
    }

    /* Reset Action on plugins page */
    public static function reset_on_plugins_page()
    {
        if ( isset($_GET['reset-ejo-client']) ) {

            $reset_ejo_client = esc_attr($_GET['reset-ejo-client']);

            if ( $reset_ejo_client == true ) {
                self::set_client_caps();

                echo '<div id="message" class="updated notice is-dismissible">';
                echo '<p>EJO Client Reset</p>';
                echo '</div>';
            }
        }
    }
}

/* Call EJO Client Admin */
EJO_Client::init();
