<?php
// Copyright by Manuel
// Support www.ilch.de
defined( 'main' ) or die( 'no direct access' );

function get_ini_menu( $file )
{
    $menus = simplexml_load_file( $file );
    $umenu = '';
    
    $tpl = new tpl( 'adminsubmenu', 1 );
    
    foreach ( $menus->list AS $liste ) {
        $tpl->set_out( 'headline', $liste->attributes()->title, 0 );
        $tpl->out( 1 );
        foreach ( $liste->modul AS $mod ) {
            if ( $mod->right >= $_SESSION[ 'authright' ] OR !isset( $mod->right ) ) {
                $tpl->set_ar_out( Array(
                     'url' => $mod->url,
                    'title' => utf8_decode( $mod->title ) 
                ), 2 );
            }
        }
        $tpl->out( 3 );
    }
    
    return $umenu;
}

$ma = $menu->get_string_ar();
arsort( $ma );

foreach ( $ma as $v ) {
    $file = 'include/admin/inc/menu/' . str_replace( '-', '_', $v ) . '.php';
    if ( file_exists( $file ) ) {
        require_once( $file );
        $php_load = true;
        break;
    }
}

foreach ( $ma as $v ) {
    $file = 'include/admin/inc/menu/' . str_replace( '-', '_', $v ) . '.xml';
    
    if ( file_exists( $file ) ) {
        get_ini_menu( $file );
        $ini_load = true;
        break;
    }
}

if ( !isset( $php_load ) AND !isset( $ini_load ) ) {
    get_ini_menu( 'include/admin/inc/menu/admin.xml' );
}
?>
