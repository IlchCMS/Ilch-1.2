<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
// Kategorien aufschlüsseln
function aktForumCats( $catAR, $trenn = 'hmenu' )
{
    $out = '';
    $i   = count( $catAR ) - 1;
    if ( $trenn == 'hmenu' ) {
        while ( $i > 0 ) {
            $out .= '<a class="smalfont" href="index.php?forum-showcat-' . $catAR[ $i ][ 'id' ] . '">' . $catAR[ $i ][ 'name' ] . '</a><b> &raquo; </b>';
            $i--;
        }
        $out .= '<a class="smalfont" href="index.php?forum-showcat-' . $catAR[ $i ][ 'id' ] . '">' . $catAR[ $i ][ 'name' ] . '</a>';
    } else {
        while ( $i > 0 ) {
            $out .= $catAR[ $i ][ 'name' ] . ' :: ';
            $i--;
        }
        $out .= $catAR[ $i ][ 'name' ];
    }
    return $out;
}
// variablen suchen und definieren.
if ( $menu->get( 1 ) == 'showcat' ) {
    $cid = escape( $menu->get( 2 ), 'integer' );
    $fid = db_result( db_query( "SELECT `b`.`id` FROM `prefix_forums` as `b` WHERE (`b`.`view`  >= " . $_SESSION[ 'authright' ] . " OR `b`.`reply` >= " . $_SESSION[ 'authright' ] . " OR `b`.`start` >= " . $_SESSION[ 'authright' ] . ") AND `b`.`cid` = " . $cid . " LIMIT 1" ), 0, 0 );
}

if ( $menu->get( 1 ) == 'showtopics' OR $menu->get( 1 ) == 'editforum' OR $menu->get( 1 ) == 'savetopic' OR $menu->get( 1 ) == 'newtopic' ) {
    $fid = escape( $menu->get( 2 ), 'integer' );
}
if ( $menu->get( 1 ) == 'showposts' OR $menu->get( 1 ) == 'newpost' OR $menu->get( 1 ) == 'editpost' OR $menu->get( 1 ) == 'edittopic' OR $menu->get( 1 ) == 'delpost' OR $menu->get( 1 ) == 'savepost' ) {
    $tid = escape( $menu->get( 2 ), 'integer' );
}
// menu
require_once( 'include/contents/forum/menu.php' );

$forum_failure = array( );
$forum_rights  = array( );
if ( !empty( $tid ) ) {
    $aktTopicAbf = "SELECT * FROM `prefix_topics` WHERE `id` = " . $tid;
    $aktTopicErg = db_query( $aktTopicAbf );
    if ( db_num_rows( $aktTopicErg ) == 1 ) {
        $aktTopicRow = db_fetch_assoc( $aktTopicErg );
        if ( empty( $fid ) ) {
            $fid = $aktTopicRow[ 'fid' ];
        }
    } else {
        $forum_failure[ ] = $lang[ 'topicidnotfound' ];
    }
}

if ( !empty( $fid ) ) {
    $aktForumAbf = "SELECT
    `a`.`id` as `cid`, `a`.`cid` as `topcid`, `a`.`name` as `cat`,`b`.`name`,`b`.`view`,`b`.`start`,`b`.`reply`
  FROM `prefix_forums` `b`
    LEFT JOIN `prefix_forumcats` `a` ON `a`.`id` = `b`.`cid`
  WHERE `b`.`id` = " . $fid;
    $aktForumErg = db_query( $aktForumAbf );
    if ( db_num_rows( $aktForumErg ) > 0 ) {
        $aktForumRow          = db_fetch_assoc( $aktForumErg );
        // Unterkategorien
        $topcid               = $aktForumRow[ 'topcid' ];
        $catsnr               = 1;
        $aktForumRow[ 'kat' ] = array( );
        while ( $topcid != 0 ) {
            $tmpsql                                    = db_fetch_object( db_query( "SELECT `id`,`cid`,`name` FROM `prefix_forumcats` WHERE `id` = " . $topcid ) );
            $topcid                                    = $tmpsql->cid;
            $aktForumRow[ 'kat' ][ $catsnr ]           = array( );
            $aktForumRow[ 'kat' ][ $catsnr ][ 'id' ]   = $tmpsql->id;
            $aktForumRow[ 'kat' ][ $catsnr ][ 'name' ] = $tmpsql->name;
            $catsnr++;
        }
        $aktForumRow[ 'kat' ][ 0 ][ 'id' ]   = $aktForumRow[ 'cid' ];
        $aktForumRow[ 'kat' ][ 0 ][ 'name' ] = $aktForumRow[ 'cat' ];
        // Unterkategorien - Ende
        $forum_rights                        = array(
             'start' => has_right( $aktForumRow[ 'start' ] ),
            'reply' => has_right( array(
                 $aktForumRow[ 'reply' ],
                $aktForumRow[ 'start' ] 
            ) ),
            'view' => has_right( array(
                 $aktForumRow[ 'view' ],
                $aktForumRow[ 'reply' ],
                $aktForumRow[ 'start' ] 
            ) ),
            'mods' => forum_user_is_mod( $fid ) 
        );
        
        if ( $forum_rights[ 'view' ] == false ) {
            $forum_failure[ ] = $lang[ 'forumidnotfound' ];
        }
    } else {
        $forum_failure[ ] = $lang[ 'forumidnotfound' ];
    }
}

switch ( $menu->get( 1 ) ) {
    default:
        $incdatei = 'show_forum.php';
        break;
    case 'showtopics':
        $incdatei = 'show_topic.php';
        break;
    case 'editforum':
        $incdatei = 'edit_forum.php';
        break;
    case 'showcat':
        $incdatei = 'show_cat.php';
        break;
    case 'showposts':
        $incdatei = 'show_posts.php';
        break;
    case 'newtopic':
        $incdatei = 'new_topic.php';
        break;
    case 'savetopic':
        $incdatei = 'save_topic.php';
        break;
    case 'newpost':
        $incdatei = 'new_post.php';
        break;
    case 'savepost':
        $incdatei = 'save_post.php';
        break;
    case 'edittopic':
        $incdatei = 'edit_topic.php';
        break;
    case 'delpost':
        $incdatei = 'del_post.php';
        break;
    case 'editpost':
        $incdatei = 'edit_post.php';
        break;
    case 'privmsg':
        $incdatei = 'privmsg.php';
        break;
    case 'aeit':
    case 'aubt':
    case 'augt':
        $incdatei = 'search.php';
        break;
    case 'search':
        $incdatei = 'suchen.php';
        break;
}

if ( isset( $incdatei ) ) {
    require_once( 'include/contents/forum/' . $incdatei );
}
// -----------------------------------------------------------|

?>