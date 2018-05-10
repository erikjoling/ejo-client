<?php 

// /**
//  * Polylang
//  */

// add_action( 'admin_menu', function() {
//     if ( ! current_user_can( 'manage_options' ) && function_exists( 'PLL' ) ) {
//         add_menu_page( __( 'Strings translations', 'polylang' ), __( 'Languages', 'polylang' ), 'edit_theme_options', 'mlang_strings', array( PLL(), 'languages_page' ), 'dashicons-translation' );
//     }
// } );