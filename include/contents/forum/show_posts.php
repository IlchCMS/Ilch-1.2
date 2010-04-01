<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
// check ob ein fehler aufgetreten ist.
check_forum_failure( $forum_failure );


$title = $allgAr[ 'title' ] . ' :: Forum :: ' . $aktTopicRow[ 'name' ] . ' :: Beitr&auml;ge zeigen';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b>' . aktForumCats( $aktForumRow[ 'kat' ] ) . '<b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-' . $fid . '">' . $aktForumRow[ 'name' ] . '</a><b> &raquo; </b>';
$hmenu .= $aktTopicRow[ 'name' ] . $extented_forum_menu_sufix;
$design = new design( $title, $hmenu, 1 );
$design->header();
// Topic Hits werden eins hochgesetzt.
db_query( 'UPDATE `prefix_topics` SET `hit` = `hit` + 1 WHERE `id` = "' . $tid . '"' );
// mehrere seiten fals gefordert
$limit  = $allgAr[ 'Fpanz' ]; // Limit
$page   = ( $menu->getA( 3 ) == 'p' ? $menu->getE( 3 ) : 1 );
$MPL    = db_make_sites( $page, "WHERE tid = " . $tid, $limit, 'index.php?forum-showposts-' . $tid, 'posts' );
$anfang = ( $page - 1 ) * $limit;

$antworten = '';
if ( ( $aktTopicRow[ 'stat' ] == 1 AND $forum_rights[ 'reply' ] == true ) OR ( $_SESSION[ 'authright' ] <= '-7' OR $forum_rights[ 'mods' ] == true ) ) {
    $antworten = '<b>[ <a href="index.php?forum-newpost-' . $tid . '">' . $lang[ 'answer' ] . '</a> ]</b>';
}

$class = 'Cmite';

$tpl = new tpl( 'forum/showpost' );
$ar  = array(
     'SITELINK' => $MPL,
    'tid' => $tid,
    'ANTWORTEN' => $antworten,
    'TOPICNAME' => $aktTopicRow[ 'name' ] 
);
$tpl->set_ar_out( $ar, 0 );
$i      = $anfang + 1;
$ges_ar = array(
     'wurstegal',
    'maennlich',
    'weiblich' 
);
$erg    = db_query( "SELECT `geschlecht`, `prefix_posts`.`id`,`txt`,`time`,`erstid`,`erst`,`sig`,`avatar`,`posts` FROM `prefix_posts` LEFT JOIN `prefix_user` ON `prefix_posts`.`erstid` = `prefix_user`.`id` WHERE `tid` = " . $tid . " ORDER BY `time` LIMIT " . $anfang . "," . $limit );
while ( $row = db_fetch_assoc( $erg ) ) {
    $class           = ( $class == 'Cnorm' ? 'Cmite' : 'Cnorm' );
    // define some vars.
    $row[ 'sig' ]    = ( empty( $row[ 'sig' ] ) ? '' : '<br /><hr style="width: 50%;" align="left">' . bbcode( $row[ 'sig' ] ) );
    $row[ 'TID' ]    = $tid;
    $row[ 'class' ]  = $class;
    $row[ 'date' ]   = date( 'd.m.Y - H:i:s', $row[ 'time' ] );
    $row[ 'delete' ] = '';
    $row[ 'change' ] = '';
    if ( !is_numeric( $row[ 'geschlecht' ] ) ) {
        $row[ 'geschlecht' ] = 0;
    }
    if ( file_exists( $row[ 'avatar' ] ) ) {
        $row[ 'avatar' ] = '<br /><br /><img src="' . $row[ 'avatar' ] . '" alt="User Pic" border="0" /><br />';
    } elseif ( $allgAr[ 'forum_default_avatar' ] ) {
        $row[ 'avatar' ] = '<br /><br /><img src="include/images/avatars/' . $ges_ar[ $row[ 'geschlecht' ] ] . '.jpg" alt="User Pic" border="0" /><br />';
    } else {
        $row[ 'avatar' ] = '';
    }
    $row[ 'rang' ] = userrang( $row[ 'posts' ], $row[ 'erstid' ] );
    $row[ 'txt' ]  = ( isset( $_GET[ 'such' ] ) ? markword( bbcode( $row[ 'txt' ] ), $_GET[ 'such' ] ) : bbcode( $row[ 'txt' ] ) );
    $row[ 'i' ]    = $i;
    $row[ 'page' ] = $page;
    
    if ( $row[ 'posts' ] != 0 ) {
        $row[ 'erst' ] = '<a href="index.php?user-details-' . $row[ 'erstid' ] . '"><b>' . $row[ 'erst' ] . '</b></a>';
    } elseif ( $row[ 'erstid' ] != 0 ) {
        $row[ 'rang' ] = 'gel&ouml;schter User';
    }
    
    if ( $forum_rights[ 'mods' ] == true AND $i > 1 ) {
    	$row['delete'] = TRUE;
    }
    if ( $forum_rights[ 'reply' ] == true AND loggedin() && $row["erstid"] == $_SESSION["authid"]) {
    	$row['change'] = TRUE;
    }
    $row[ 'posts' ] = ( $row[ 'posts' ] ? '<br />Posts: ' . $row[ 'posts' ] : '' ) . '<br />';
    
    $row['NEW'] = post_is_new($row["time"], $tid, $fid) ? "true" : "false";
    
    $tpl->set_ar_out( $row, 1 );
    
    $i++;
}

$tpl->set_ar_out( array(
     'SITELINK' => $MPL,
    'ANTWORTEN' => $antworten 
), 2 );

if ( loggedin() ) {
    if ( $menu->get( 3 ) == 'topicalert' ) {
        if ( 1 == db_result( db_query( "SELECT COUNT(*) FROM `prefix_topic_alerts` WHERE `uid` = " . $_SESSION[ 'authid' ] . " AND `tid` = " . $tid ), 0 ) ) {
            db_query( "DELETE FROM `prefix_topic_alerts` WHERE `uid` = " . $_SESSION[ 'authid' ] . " AND `tid` = " . $tid );
        } else {
            db_query( "INSERT INTO `prefix_topic_alerts` (`tid`,`uid`) VALUES (" . $tid . ", " . $_SESSION[ 'authid' ] . ")" );
        }
    }
    
    echo 'Optionen:';
    if ( 1 == db_result( db_query( "SELECT COUNT(*) FROM `prefix_topic_alerts` WHERE `uid` = " . $_SESSION[ 'authid' ] . " AND `tid` = " . $tid ), 0 ) ) {
        echo '<br />- <a href="index.php?forum-showposts-' . $tid . '-topicalert">' . $lang[ 'nomailonreply' ] . '</a><br />';
    } else {
        echo '<br />- <a href="index.php?forum-showposts-' . $tid . '-topicalert">' . $lang[ 'mailonreply' ] . '</a><br />';
    }
}

if ( $forum_rights[ 'mods' ] == true ) {
    $tpl->set( 'status', ( $aktTopicRow[ 'stat' ] == 1 ? $lang[ 'close' ] : $lang[ 'open' ] ) );
    $tpl->set( 'festnorm', ( $aktTopicRow[ 'art' ] == 0 ? $lang[ 'fixedtopic' ] : $lang[ 'normaltopic' ] ) );
    $tpl->set( 'tid', $tid );
    $tpl->out( 3 );
}

// toipc als gelesen markieren
$_SESSION[ 'forumSEE' ][ $fid ][ $tid ] = time();

$design->footer();

?>