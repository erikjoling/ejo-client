<?php 

/* Get gravityforms caps */
function ejo_get_gravityforms_caps() 
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
