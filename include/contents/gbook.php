<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: G&auml;stebuch';
$hmenu  = 'G&auml;stebuch';
$design = new design( $title, $hmenu );
$design->header();
// time sperre in sekunden
$timeSperre = $allgAr[ 'Gsperre' ];

/*
 * gbook
 * id , name , mail , page , ip , time , txt
 */

/**
 * zeigt den preview des texts an
 * nur aufrufen, wenn in $_POST["txt"] der text steht
 */
function showPreview() {
	$tpl = new tpl('gbook');
	$tpl->set("TEXT", BBcode(escape($_POST["txt"], "textarea")));
	$tpl->out('preview');
}

/**
 * Zeigt das Formular an, in dem User ihre Einträge machen können
 * @param $text Vorbelegung für den text
 * @param $mail Vorbelegung für die Emailadresse
 * @param $page Vorbelegung für die Homepage
 */
function showForm($text = "", $mail = "", $page = "") {
	global $allgAr;
	
	$tpl = new tpl( 'gbook.htm' );
	$ar  = array(
             'uname' => $_SESSION[ 'authname' ],
            'SMILIES' => getsmilies(),
            'ANTISPAM' => get_antispam( 'gbook', 1 ),
            'TXTL' => $allgAr[ 'Gtxtl' ] ,
			'TEXT' => $text,
			'PAGE' => $page,
			'MAIL' => $mail
	);
	$tpl->set_ar_out( $ar, "formular_eintrag" );

	if ( !isset( $_SESSION[ 'klicktime_gbook' ] ) ) {
		$_SESSION[ 'klicktime_gbook' ] = 0;
	}

}

switch ( $menu->get( 1 ) ) {
	case 1:
		showForm();
		break;
	case 2:
		// vorschau
		if(isset($_POST["preview"])) {
			showPreview();
			showForm($_POST["txt"], $_POST["mail"], $_POST["page"]);
		} else {
			$dppk_time = time();

			if ( ( $_SESSION[ 'klicktime_gbook' ] + $timeSperre ) < $dppk_time AND isset( $_POST[ 'name' ] ) AND isset( $_POST[ 'txt' ] ) AND trim( $_POST[ 'name' ] ) != "" AND trim( $_POST[ 'txt' ] ) != "" AND chk_antispam( 'gbook' ) AND strlen( $_POST[ 'txt' ] ) <= $allgAr[ 'Gtxtl' ] ) {
				$txt  = escape( $_POST[ 'txt' ], 'textarea' );
				if ($_SESSION['authid'] == 0) {
					$name = escape_nickname( $_POST[ 'name' ], 'string' ).' (Gast)';
				} else {
					$name = escape_nickname( $_POST[ 'name' ], 'string' );
				}
				$mail = escape( $_POST[ 'mail' ], 'string' );
				$page = escape( $_POST[ 'page' ], 'string' );

				db_query( "INSERT INTO `prefix_gbook` (`name`,`mail`,`page`,`time`,`ip`,`txt`) VALUES ('" . $name . "', '" . $mail . "', '" . $page . "', '" . time() . "', '" . getip() . "', '" . $txt . "')" );

				$_SESSION[ 'klicktime_gbook' ] = $dppk_time;
				wd( 'index.php?gbook', $lang[ 'insertsuccessful' ] );
			} else {
				echo '- ' . $lang[ 'donotpostsofast' ];
				echo '<br />- ' . sprintf( $lang[ 'gbooktexttolong' ], $allgAr[ 'Gtxtl' ] );
				echo '<br />- ' . $lang[ 'plsfilloutallfields' ];
			}
		}
		break;
	case 'show':
		if ( $allgAr[ 'gbook_koms_for_inserts' ] == 1 ) {
			$id = escape( $menu->get( 2 ), 'integer' );
			if ( chk_antispam( 'gbookkom' ) AND isset( $_POST[ 'name' ] ) AND isset( $_POST[ 'text' ] ) ) {
				$name = escape( $_POST[ 'name' ], 'string' );
				$text = escape( $_POST[ 'text' ], 'string' );
				db_query( "INSERT INTO `prefix_koms` (`name`,`text`,`uid`,`cat`) VALUES ('" . $name . "', '" . $text . "', " . $id . ", 'GBOOK')" );
			}
			if ( $menu->getA( 3 ) == 'd' AND is_numeric( $menu->getE( 3 ) ) AND has_right( -7, 'gbook' ) ) {
				$did = escape( $menu->getE( 3 ), 'integer' );
				db_query( "DELETE FROM `prefix_koms` WHERE `uid` = " . $id . " AND `cat` = 'GBOOK' AND `id` = " . $did );
			}

			$r            = db_fetch_assoc( db_query( "SELECT `time`, `name`, `mail`, `page`, `txt` as `text`, `id` FROM `prefix_gbook` WHERE `id` = " . $id ) );
			$r[ 'datum' ] = date( 'd.m.Y', $r[ 'time' ] );
			if ( $r[ 'page' ] != '' ) {
				$r[ 'page' ] = get_homepage( $r[ 'page' ] );
				$r[ 'page' ] = ' &nbsp; <a href="' . $r[ 'page' ] . '" target="_blank"><img src="include/images/icons/page.gif" border="0" alt="Homepage ' . $lang[ 'from' ] . ' ' . $r[ 'name' ] . '"></a>';
			}
			if ( $r[ 'mail' ] != '' ) {
				$r[ 'mail' ] = ' &nbsp; <a href="mailto:' . escape_email_to_show( $r[ 'mail' ] ) . '"><img src="include/images/icons/mail.gif" border="0" alt="E-Mail ' . $lang[ 'from' ] . ' ' . $r[ 'name' ] . '"></a>';
			}

			$tpl             = new tpl( 'gbook.htm' );
			$r[ 'ANTISPAM' ] = get_antispam( 'gbookkom', 100 );
			$r[ 'uname' ]    = $_SESSION[ 'authname' ];
			$r[ 'text' ]     = bbcode( $r[ 'text' ] );
			$tpl->set_ar_out( $r, 4 );
			$i   = 1;
			$erg = db_query( "SELECT `id`, `name`, `text` FROM `prefix_koms` WHERE `uid` = " . $id . " AND `cat` = 'GBOOK' ORDER BY `id` DESC" );
			$anz = db_num_rows( $erg ) + 1;
			while ( $r1 = db_fetch_assoc( $erg ) ) {
				$r1[ 'zahl' ] = $anz - $i;
				$r1[ 'text' ] = bbcode( $r1[ 'text' ] );
				if ( has_right( -7, 'gbook' ) ) {
					$r1[ 'text' ] .= '<a href="index.php?gbook-show-' . $id . '-d' . $r1[ 'id' ] . '"><img src="include/images/icons/del.gif" alt="' . $lang[ 'delete' ] . '" border="0" title="' . $lang[ 'delete' ] . '" /></a>';
				}
				$tpl->set_ar_out( $r1, 5 );
				$i++;
			}
			$tpl->out( 6 );
		}
		break;
	default:

		$limit  = $allgAr[ 'gbook_posts_per_site' ]; // Limit
		$page   = ( $menu->getA( 1 ) == 'p' ? escape( $menu->getE( 1 ), 'integer' ) : 1 );
		$MPL    = db_make_sites( $page, "", $limit, "?gbook", 'gbook' );
		$anfang = ( $page - 1 ) * $limit;

		$tpl = new tpl( 'gbook.htm' );

		$ei1 = @db_query( "SELECT COUNT(ID) FROM `prefix_gbook`" );
		$ein = @db_result( $ei1, 0 );

		$ar = array(
             'EINTRAGE' => $ein 
		);
		$tpl->set_ar_out( $ar, 0 );

		$erg = db_query( "SELECT * FROM `prefix_gbook` ORDER BY `time` DESC LIMIT " . $anfang . "," . $limit ) or die( db_error() );
		while ( $row = db_fetch_object( $erg ) ) {
			$page = '';
			$mail = '';
			if ( $row->page ) {
				$row->page = get_homepage( $row->page );
				$page      = ' &nbsp; <a href="' . $row->page . '" target="_blank"><img src="include/images/icons/page.gif" border="0" alt="Homepage ' . $lang[ 'from' ] . ' ' . $row->name . '"></a>';
			}
			if ( $row->mail ) {
				$mail = ' &nbsp; <a href="mailto:' . escape_email_to_show( $row->mail ) . '"><img src="include/images/icons/mail.gif" border="0" alt="E-Mail ' . $lang[ 'from' ] . ' ' . $row->name . '"></a>';
			}
			$koms = '';
			if ( $allgAr[ 'gbook_koms_for_inserts' ] == 1 ) {
				$koms = db_result( db_query( "SELECT COUNT(*) FROM `prefix_koms` WHERE `uid` = " . $row->id . " AND `cat` = 'GBOOK'" ), 0, 0 );
				$koms = '<a href="index.php?gbook-show-' . $row->id . '">' . $koms . ' ' . $lang[ 'comments' ] . '</a>';
			}

			$ar = array(
                 'NAME' => $row->name,
                'DATE' => date( "d.m.Y", $row->time ),
                'koms' => $koms,
                'MAIL' => $mail,
                'ID' => $row->id,
                'PAGE' => $page,
                'TEXT' => BBCode( $row->txt ) 
			);

			$tpl->set_ar_out( $ar, 1 );
		}
		$tpl->set_out( 'SITELINK', $MPL, 2 );
		break;
}
// -----------------------------------------------------------|
$design->footer();

?>