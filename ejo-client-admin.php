<?php
/**
 * Plugin Name:         EJO Client Admin
 * Plugin URI:          http://github.com/erikjoling/ejo-client-admin
 * Description:         Improved permissions and user experience for EJOweb clients.
 * Version:             0.1
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
    public static $version = '0.1';

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
        // add_action( 'init', array( 'EJO_Client_Admin', 'register_client_role' ) );

        /* Add uninstall hook */
        register_uninstall_hook( __FILE__, array( 'EJO_Client_Admin', 'on_plugin_uninstall') );
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

    /**
     * Register client role 
     * 
     * [TO-DO] Need to add error message 
     */
    public static function register_client_role() 
    {
        /* Check if new role already exists */
        if ( get_role( EJO_Client_Admin::$role_name ) ) {
            write_log('HALT: client-admin bestaat al');
            return; // Role already exists
        }

        /* Get editor-role (the new client-role is based on this) */
        $editor = get_role( 'editor' );

        /* Check if editor-role exists */
        if ( $editor == NULL ) {
            write_log('HALT: Editor role bestaat nog niet');
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
            'export' => 1,
        );

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

    /* Unregister client role */
    public static function unregister_client_role()
    {
        /* When a role is removed, the users who have this role lose all rights on the site */
    }
}

/* Call EJO Client Admin */
EJO_Client_Admin::instance();