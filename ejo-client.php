<?php
/**
 * Plugin Name:         EJO Client
 * Plugin URI:          https://github.com/erikjoling/ejo-client
 * Description:         Improved permissions and user experience for EJOweb clients.
 * Version:             0.1.2
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
    public static $version = '0.1.2';

    /* Stores the handle of this plugin */
    public static $handle;

    /* Stores the directory path for this plugin. */
    public static $dir;

    /* Stores the directory URI for this plugin. */
    public static $uri;

    /* Name of client role */
    public static $role_name = 'client';

    //* Blog activated for client
    public static $blog_enabled = false;

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
        add_action( 'plugins_loaded', array( 'EJO_Client', 'setup' ), 1 );

        /* Load Translations */
        add_action( 'plugins_loaded', array( 'EJO_Client', 'load_textdomain' ), 2 );

        /* Add activation hook */
        register_activation_hook( __FILE__, array( 'EJO_Client', 'on_plugin_activation') );

        /* Add uninstall hook */
        register_uninstall_hook( __FILE__, array( 'EJO_Client', 'on_plugin_uninstall') );
        register_deactivation_hook( __FILE__, array( 'EJO_Client', 'on_plugin_uninstall') );

        /* Change Wordpress SEO capability to edit_theme_options */
        add_filter( 'wpseo_manage_options_capability', function(){
            return 'edit_theme_options';
        } );

        add_action( 'after_setup_theme', array( 'EJO_Client', 'test') );
    }

    public static function test() {

    }

    /* Defines the directory path and URI for the plugin. */
    public static function setup() 
    {
        EJO_Client::$handle = dirname( plugin_basename( __FILE__ ) );
        EJO_Client::$dir = plugin_dir_path( __FILE__ );
        EJO_Client::$uri = plugin_dir_url( __FILE__ );
    }

    /* Load Translations */
    public static function load_textdomain() 
    {
        /* Load the translation for the plugin */
        load_plugin_textdomain(EJO_Client::$handle, false, EJO_Client::$handle . '/languages' );
    }

    /* Fire when activating this plugin */
    public static function on_plugin_activation()
    {
        EJO_Client::register_client_role();
    }

    /* Fire when uninstalling this plugin */
    public static function on_plugin_uninstall()
    {
        EJO_Client::unregister_client_role();
    }
    
    /* Register client role */
    public static function register_client_role() 
    {
        //* Remove 
        remove_role( EJO_Client::$role_name );

        /* Add new role */
        add_role( EJO_Client::$role_name, __( 'Client' ) );

        /* Get new client role */
        $client_role = get_role( EJO_Client::$role_name );

        // If the role exists, add the capabilities
        if ( ! is_null( $client_role ) ) {

            //* Manage client capabilities 
            foreach (EJO_Client::get_client_caps() as $caps) {                

                //* Add client caps
                $client_role->add_cap( $caps );
            }

            //* Manage gravityforms capabilities
            foreach (EJO_Client::get_gravityforms_caps() as $caps) {                

                //* Add Gravityforms caps
                $client_role->add_cap( $caps );
            }

            //* Check if blog is enabled for client
            if (! EJO_Client::$blog_enabled) {

                //* Remove blog capabilities
                foreach (EJO_Client::get_blog_caps() as $caps) {     

                    //* Remove Blog caps
                    $client_role->remove_cap( $caps );
                }
            }

            //* Check if contactad plugin is activated
            if ( class_exists( 'EJO_Contactads' ) ) {

                //* Add contactad capabilities
                foreach (EJO_Contactads::get_caps() as $caps) {                

                    $client_role->add_cap( $caps );
                }
            }
        }
    }

    /* Get the right caps for client */
    public static function get_client_caps() 
    {
        return array(/* Get the right caps for gravityforms */
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
            // 'export',
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
    }

    /* Get gravityforms caps */
    public static function get_gravityforms_caps() 
    {
        return array(
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
    }

    /* Blog capabilities */
    public static function get_blog_caps() 
    {
        /* Blog capabilities to remove */
        return array(
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
    }

    /* Unregister client role */
    public static function unregister_client_role()
    {
        /* Remove client role */
        remove_role( EJO_Client::$role_name );

        /** 
         * When a role is removed, the users who have this role lose all rights on the site 
         * Maybe assign them to editor role
         */
    }
}

/* Call EJO Client Admin */
EJO_Client::init();
