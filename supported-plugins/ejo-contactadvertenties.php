<?php

/* Get EJO contactadverntie capabilities */
function get_ejo_contactadvertentie_caps() 
{
    //* Filter if EJO Contactads should be included even if plugin is installed
    $is_ejo_contactadvertenties_enabled = apply_filters( 'ejo_client_ejo-contactadvertenties_enabled', true );

    if ( ! class_exists( 'EJO_Contactads' )  || ! $is_ejo_contactadvertenties_enabled ) 
        return array();

    return apply_filters( 'ejo_client_ejo-contactadvertenties_caps', EJO_Contactads::get_caps() );
}