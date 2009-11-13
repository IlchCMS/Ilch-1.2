<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );

$design = new design( 'Ilch Admin-Control-Panel :: News', '', 2 );
$design->header();
// -----------------------------------------------------------|
// #
// ##
// ###
// #### F u n k t i o n e n
function getKats( $akt )
{
    $katAR = array( );
    $kats  = '';
    $erg   = db_query( "SELECT DISTINCT `news_kat` FROM `prefix_news`" );
    while ( $row = db_fetch_object( $erg ) ) {
        $katAr[ ] = $row->news_kat;
    }
    $katAr[ ] = 'Allgemein';
    $katAr    = array_unique( $katAr );
    foreach ( $katAr as $a ) {
        if ( trim( $a ) == trim( $akt ) ) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $kats .= '<option' . $sel . '>' . $a . '</option>';
    }
    return ( $kats );
}
// #### F u n k t i o n
// ###
// ##
// #
// #
// ##
// ###
// #### A k t i o n e n
if ( !empty( $_REQUEST[ 'um' ] ) ) {
    $um                = $_REQUEST[ 'um' ];
    $_POST[ 'titel' ]  = escape( $_POST[ 'titel' ], 'string' );
    $_POST[ 'grecht' ] = escape( $_POST[ 'grecht' ], 'integer' );
    $_POST[ 'kat' ]    = escape( $_POST[ 'kat' ], 'string' );
    $_POST[ 'katLis' ] = escape( $_POST[ 'katLis' ], 'string' );
    $_POST[ 'newsID' ] = escape( $_POST[ 'newsID' ], 'integer' );
    if ( $um == 'insert' ) {
        // insert
        $text = escape( $_POST[ 'txt' ], 'textarea' );
        if ( $_POST[ 'katLis' ] == 'neu' ) {
            $_POST[ 'katLis' ] = $_POST[ 'kat' ];
        }
        db_query( "INSERT INTO `prefix_news` (`news_title`,`user_id`,`news_time`,`news_recht`,`news_kat`,`news_text`)
		VALUES ('" . $_POST[ 'titel' ] . "'," . $_SESSION[ 'authid' ] . ",NOW()," . $_POST[ 'grecht' ] . ",'" . $_POST[ 'katLis' ] . "','" . $text . "')" );
        // insert
    } elseif ( $um == 'change' ) {
        // edit
        $text = escape( $_POST[ 'txt' ], 'textarea' );
        
        if ( $_POST[ 'katLis' ] == 'neu' ) {
            $_POST[ 'katLis' ] = $_POST[ 'kat' ];
        }
        db_query( 'UPDATE `prefix_news` SET
				`news_title` = "' . $_POST[ 'titel' ] . '",
				`user_id`  = "' . $_SESSION[ 'authid' ] . '",
				`news_recht` = "' . $_POST[ 'grecht' ] . '",
				`news_kat`   = "' . $_POST[ 'katLis' ] . '",
				`news_text`  = "' . $text . '" WHERE `news_id` = "' . $_POST[ 'newsID' ] . '" LIMIT 1' );
        $edit = $_POST[ 'newsID' ];
    }
}
// edit
// del
if ( $menu->get( 1 ) == 'del' ) {
    db_query( 'DELETE FROM `prefix_news` WHERE `news_id` = "' . $menu->get( 2 ) . '" LIMIT 1' );
}
// del
// #### A k t i o n e n
// ###
// ##
// #
// #
// ##
// ###
// #### h t m l   E i n g a b e n
if ( empty( $doNoIn ) ) {
    $limit  = 20; // Limit
    $page   = ( $menu->getA( 1 ) == 'p' ? $menu->getE( 1 ) : 1 );
    $MPL    = db_make_sites( $page, '', $limit, "?news", 'news' );
    $anfang = ( $page - 1 ) * $limit;
    if ( $menu->get( 1 ) != 'edit' ) {
        $FnewsID = '';
        $Faktion = 'insert';
        $Fueber  = '';
        $Fstext  = '';
        $Ftxt    = '';
        $Fgrecht = '';
        $FkatLis = '';
        $Fsub    = 'Eintragen';
    } else {
        $row     = db_fetch_object( db_query( "SELECT * FROM `prefix_news` WHERE `news_id` = " . $menu->get( 2 ) ) );
        $FnewsID = $row->news_id;
        $Faktion = 'change';
        $Fueber  = $row->news_title;
        $Ftxt    = stripslashes( $row->news_text );
        $Fgrecht = $row->news_recht;
        $FkatLis = $row->news_kat;
        $Fsub    = '&Auml;ndern';
    }
    $tpl = new tpl( 'news', 1 );
    
    $ar = array(
         'NEWSID' => $FnewsID,
        'AKTION' => $Faktion,
        'MPL' => $MPL,
        'UEBER' => $Fueber,
        'txt' => $Ftxt,
        'SMILIS' => getsmilies(),
        'grecht' => dbliste( $Fgrecht, $tpl, 'grecht', "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC" ),
        'KATS' => getKats( $FkatLis ),
        'FSUB' => $Fsub 
        
    );
    
    $tpl->set_ar_out( $ar, 0 );
    // e d i t , d e l e t e
    $abf = 'SELECT `news_id`,`news_title`
	        FROM `prefix_news`
					ORDER BY `news_time` DESC
					LIMIT ' . $anfang . ',' . $limit;
    
    $erg   = db_query( $abf );
    $class = '';
    while ( $row = db_fetch_object( $erg ) ) {
        $class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
        $tpl->set_ar_out( array(
             'ID' => $row->news_id,
            'class' => $class,
            'TITEL' => $row->news_title 
        ), 1 );
    }
    // e d i t , d e l e t e
    $tpl->set_ar_out( array(
         'MPL' => $MPL 
    ), 2 );
}

$design->footer();

?>