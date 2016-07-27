<?php
/**
 * Plugin Name:         EJO Client Admin
 * Plugin URI:          https://github.com/erikjoling/ejo-client-admin
 * Description:         Improved permissions and user experience for EJOweb clients.
 * Version:             0.1.1
 * Author:              Erik Joling
 * Author URI:          https://www.ejoweb.nl/
 * Text Domain:         ejo-client-admin
 * Domain Path:         /languages
 *
 * GitHub Plugin URI:   https://github.com/erikjoling/ejo-client-admin
 * GitHub Branch:       master
 *
 * Minimum PHP version: 5.3.0
 */

/**
 *
 */
final class EJO_Client_Admin
{
    /* Version number of this plugin */
    public static $version = '0.1.1';

    /* Holds the instance of this class. */
    protected static $_instance = null;

    /* Stores the handle of this plugin */
    public static $handle;

    /* Stores the directory path for this plugin. */
    public static $dir;

    /* Stores the directory URI for this plugin. */
    public static $uri;

    /* Name of client role */
    public static $role_name = 'ejo-client';

    /* Returns the instance. */
    public static function instance() 
    {
        if ( !self::$_instance )
            self::$_instance = new self;
        return self::$_instance;
    }

    /* Plugin setup. */
    protected function __construct() 
    {
        /* Setup */
        add_action( 'plugins_loaded', array( 'EJO_Client_Admin', 'setup' ), 1 );

        /* Load Translations */
        add_action( 'plugins_loaded', array( 'EJO_Client_Admin', 'load_textdomain' ), 2 );

        /* Add activation hook */
        register_activation_hook( __FILE__, array( 'EJO_Client_Admin', 'on_plugin_activation') );

        /* Add uninstall hook */
        register_uninstall_hook( __FILE__, array( 'EJO_Client_Admin', 'on_plugin_uninstall') );
        // register_deactivation_hook( __FILE__, array( 'EJO_Client_Admin', 'on_plugin_uninstall') );

        /* Change Wordpress SEO capability to edit_theme_options */
        add_filter( 'wpseo_manage_options_capability', function(){
            return 'edit_theme_options';
        } );
    }

    /* Fire when activating this plugin */
    public static function on_plugin_activation()
    {
        EJO_Client_Admin::register_client_role();
    }

    /* Fire when uninstalling this plugin */
    public static function on_plugin_uninstall()
    {
        EJO_Client_Admin::unregister_client_role();
    }
    
    /* Defines the directory path and URI for the plugin. */
    public static function setup() 
    {
        EJO_Client_Admin::$handle = dirname( plugin_basename( __FILE__ ) );
        EJO_Client_Admin::$dir = plugin_dir_path( __FILE__ );
        EJO_Client_Admin::$uri = plugin_dir_url( __FILE__ );
    }

    /* Load Translations */
    public static function load_textdomain() 
    {
        /* Load the translation for the plugin */
        load_plugin_textdomain(EJO_Client_Admin::$handle, false, EJO_Client_Admin::$handle . '/languages' );
    }

    /* Register client role */
    public static function register_client_role() 
    {
        /**
         * Remove client role 
         * Due to shortcut of add_role function it won't edit capabilities otherwise
         */
        remove_role( EJO_Client_Admin::$role_name );

        /* Get editor-role (the new client-role is based on this) */
        $editor = get_role( 'editor' );

        /* Check if editor-role exists */
        if ( $editor == NULL ) {
            /* [TO-DO] Need to add error message */
            return; // Editor role does not exist
        }

        /* List of capabilities to add to client-role (based on admin-role) */
        $client_capabilities_to_add = array(
            // 'switch_themes' => 1,
            // 'edit_themes' => 1,
            // 'activate_plugins' => 1,
            // 'edit_plugins' => 1,
            // 'edit_users' => 1,
            // 'edit_files' => 1,
            // 'manage_options' => 1,
            // 'import' => 1,
            // 'level_10' => 1,
            // 'level_9' => 1,
            // 'level_8' => 1,
            // 'delete_users' => 1,
            // 'create_users' => 1,
            // 'unfiltered_upload' => 1,
            // 'edit_dashboard' => 1,
            // 'update_plugins' => 1,
            // 'delete_plugins' => 1,
            // 'install_plugins' => 1,
            // 'update_themes' => 1,
            // 'install_themes' => 1,
            // 'update_core' => 1,
            // 'list_users' => 1,
            // 'remove_users' => 1,
            // 'promote_users' => 1,
            'edit_theme_options' => 1,
            // 'delete_themes' => 1,
            // 'export' => 1,
        );

        /* Get the right caps for gravityforms */
        $gravityforms_caps = EJO_Client_Admin::get_gravityforms_caps();

        /* Merge capabilities which have to be added */
        $client_capabilities_to_add = array_merge( $client_capabilities_to_add, $gravityforms_caps );

        /* Merge editor-capabilities with some specific admin-capabilities */
        $client_capabilities = apply_filters( 
            'ejo_client_capabilities', 
            array_merge( $editor->capabilities, $client_capabilities_to_add ), 
            $editor->capabilities, 
            $client_capabilities_to_add 
        );

        /* Add new role */
        add_role( EJO_Client_Admin::$role_name, __( 'Client' ), $client_capabilities );
    }

    /* Get the right caps for gravityforms */
    public static function get_gravityforms_caps() 
    {
        return array(
            'gravityforms_edit_forms' => 1,
            'gravityforms_delete_forms' => 1,
            'gravityforms_create_form' => 1,
            'gravityforms_view_entries' => 1,
            'gravityforms_edit_entries' => 1,
            'gravityforms_delete_entries' => 1,
            // 'gravityforms_view_settings' => 1,
            // 'gravityforms_edit_settings' => 1,
            // 'gravityforms_export_entries' => 1,
            // 'gravityforms_uninstall' => 1,
            'gravityforms_view_entry_notes' => 1,
            'gravityforms_edit_entry_notes' => 1,
            // 'gravityforms_view_updates' => 1,
            // 'gravityforms_view_addons' => 1,
            // 'gravityforms_preview_forms' => 1,
        );
    }


    /* Unregister client role */
    public static function unregister_client_role()
    {
        /* Remove client role */
        remove_role( EJO_Client_Admin::$role_name );

        /** 
         * When a role is removed, the users who have this role lose all rights on the site 
         * Maybe assign them to editor role
         */
    }
}

/* Call EJO Client Admin */
EJO_Client_Admin::instance();