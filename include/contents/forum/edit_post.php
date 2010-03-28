<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

if ( $forum_rights[ 'reply' ] == false ) {
    $forum_failure[ ] = $lang[ 'nopermission' ];
    check_forum_failure( $forum_failure );
}
// definie oid
$oid = escape( $menu->get( 3 ), 'integer' );

$title = $allgAr[ 'title' ] . ' :: Forum :: ' . aktForumCats( $aktForumRow[ 'kat' ], 'title' ) . ' :: ' . $aktForumRow[ 'name' ] . ' :: ' . $aktTopicRow[ 'name' ] . ' :: Beitrag &auml;ndern';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats( $aktForumRow[ 'kat' ] ) . '<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow[ 'name' ] . '</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-' . $tid . '">' . $aktTopicRow[ 'name' ] . '</a>' . $extented_forum_menu_sufix;
$design = new design( $title, $hmenu, 1 );
$design->header();

if ( !loggedin() ) {
    echo 'Gäste dürfen keine Beiträge editieren<br/><a href="index.php?user-regist">Registrieren</a> / <a href="index.php?user-login">Einloggen</a> um deine Beiträge editieren zu können';
    $design->footer( 1 );
}

$row = @db_fetch_object( @db_query( "SELECT `txt`,`erstid` FROM `prefix_posts` WHERE `id` = " . $oid ) );
if ( $_SESSION[ 'authid' ] != $row->erstid AND $forum_rights[ 'mods' ] == false ) {
    echo $lang[ 'nopermission' ];
    $design->footer( 1 );
}

list( $usec, $sec ) = explode( " ", microtime() );
$dppk_time = (float) $usec + (float) $sec;
$time      = time();
if ( !isset( $_SESSION[ 'klicktime' ] ) ) {
    $_SESSION[ 'klicktime' ] = 0;
}

$txt = '';
if ( isset( $_POST[ 'txt' ] ) ) {
    $txt = trim( escape( $_POST[ 'txt' ], 'textarea' ) );
}

if ( $_SESSION[ 'klicktime' ] > ( $dppk_time - 15 ) OR empty( $txt ) OR !empty( $_POST[ 'priview' ] ) ) {
    $tpl = new tpl( 'forum/postedit' );
    
    if ( isset( $_POST[ 'priview' ] ) ) {
        $tpl->set_out( 'txt', bbcode( unescape( $txt ) ), 0 );
    }
    
    if ( empty( $txt ) ) {
        $txt = $row->txt;
    }
    
    $ar = array(
         'tid' => $tid,
        'oid' => $oid,
        'txt' => ( isset( $_POST[ 'priview' ] ) ? escape_for_fields( unescape( $txt ) ) : escape_for_fields( $txt ) ),
        'SMILIES' => getsmilies() 
    );
    $tpl->set_ar_out( $ar, 1 );
    $erg = db_query( 'SELECT `erst`, `txt` FROM `prefix_posts` WHERE `tid` = "' . $tid . '" ORDER BY `time` DESC LIMIT 0,5' );
    while ( $row = db_fetch_assoc( $erg ) ) {
        $row[ 'txt' ] = bbcode( $row[ 'txt' ] );
        $tpl->set_ar_out( $row, 2 );
    }
    $tpl->out( 3 );
} else {
    $s = preg_quote( $lang[ 'postlastchangedby' ] );
    if ( preg_match( "/.*" . $s . " ([^\ ])* am \d\d\.\d\d\.\d\d\d\d - \d\d:\d\d:\d\d$/", $txt ) ) {
        $txt = preg_replace( "/" . $s . " ([^\ ])* am \d\d\.\d\d\.\d\d\d\d - \d\d:\d\d:\d\d$/", $lang[ 'postlastchangedby' ] . ' ' . $_SESSION[ 'authname' ] . ' am ' . date( "d.m.Y - H:i:s" ), $txt );
    } else {
        $txt .= "\n\n\n" . $lang[ 'postlastchangedby' ] . ' ' . $_SESSION[ 'authname' ] . ' am ' . date( "d.m.Y - H:i:s" );
    }
    
    db_query( "UPDATE `prefix_posts` SET `txt` = '" . $txt . "' WHERE `id` = " . $oid );
    
    $page = ceil( ( $aktTopicRow->rep + 1 ) / $allgAr[ 'Fpanz' ] );
    wd( 'index.php?forum-showposts-' . $tid . '-p' . $page . '#' . $oid, $lang[ 'changepostsuccessful' ] );
}

$design->footer();

?>