<?php

/**
 * Redirection Plugin
 */

//* Let ejo-client access redirections
add_filter( 'redirection_role', 'get_ejo_client_capability' );

/**
 * Yoast WordPress SEO Plugin
 */

//* Edit main SEO capability
// add_filter( 'wpseo_manage_options_capability',                          'get_ejo_client_capability' );

// //* Change capability necessary to save options
// add_filter( 'option_page_capability_yoast_wpseo_titles_options',        'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_internallinks_options', 'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_permalinks_options',    'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_rss_options',           'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_xml_sitemap_options',   'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_social_options',        'get_ejo_client_capability' );
// add_filter( 'option_page_capability_yoast_wpseo_options',               'get_ejo_client_capability' );

/* Wordpress SEO capability */
function get_ejo_client_capability( $capability )
{
    //* Downgrade capability from manage_options to edit_theme_options to support ejo-client
    $capability = 'edit_theme_options';        

    return $capability;
}

function ejo_get_wpseo_caps () {

    if ( ! function_exists('wpseo_init') )
        return array();

    $wpseo_caps = array(
        'wpseo_manage_options',
        'wpseo_edit_advanced_metadata',
    );

    return $wpseo_caps;
}