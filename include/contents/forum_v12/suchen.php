<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de

/**
 * @name    IlchBB Forum
 * @version 3.1
 * @author  Florian Koerner
 * @link    http://www.koerner-ws.de/
 * @license GNU General Public License
 */


defined ('main') or die ( 'no direct access' );

// IlchBB Forum 3.1 :: Loader :: Start
require_once ('include/contents/forum_v12/loader.php');
// IlchBB Forum 3.1 :: Loader :: Ende

$title = $allgAr['title'].' :: Save Post';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?search">Suchen</a>'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

// IlchBB Forum 3.1 :: Extensions :: Start
$ilchbb_tpl = new tpl('forum_v12/load_extensions');
$ilchbb_tpl->out(0);
// IlchBB Forum 3.1 :: Extensions :: End
?>

<form action="?forum-6" method="POST">
    <input type="text" value="<?php echo $such; ?>" name="such" size="30">
    &nbsp;<input type="submit" value="Suchen" name="submit">

</form>
<hr>
<br />

<?php
$i = 0;
if ( ! empty ($such) ) {
    if ( !empty ($jforum) ) {
        $abf = 'SELECT DISTINCT a.tid, b.name
					  FROM `prefix_posts` a
		        INNER JOIN `prefix_topic` b ON a.tid = b.id
						WHERE txt LIKE "%'.$such.'%"';

        $such_string = '<b>Forum</b><br><br>';

        $erg = db_query($abf);
        while ($row = db_fetch_object($erg)) {

            $i++;
            $such_string .= '<a href="?forum-2&fid=&tid='.$row->tid.'&such=';
            $such_string .= $such.'">'.$row->name.'</a><br />';

        }
    } // ende forum durchsuchen
    if ( !empty($jnews) AND !empty($jforum) ) {
        $such_string .= '</td><td class="Cnorm" valign="top">';
    }

    if ( !empty ($jnews) ) {
        $such_string .= '<b>News</b><br><br>';
        $abf = 'SELECT
	            titel,id
						FROM 
						  prefix_news
		        WHERE
							text LIKE "%'.$such.'%" 
						ORDER
						  BY time DESC';	
        $erg = db_query($abf);
        while ($row = db_fetch_object($erg)) {
            $i++;
            $such_string .= '<a href="?news-1&nid=';
            $such_string .= $row->id.'&such=';
            $such_string .= $such.'">'.$row->titel.'</a><br />';

        }
    }
    if ($i > 0) {
        echo $i.' Treffer f&uuml;r die suche nach: '.$such;
        echo '<table width="100%" cellpadding="10" border="0" cellspacing="1" class="border"><tr><td class="Cnorm" valign="top">';
        echo $such_string;
        echo '</td></tr></table>';
    } else {
        echo 'Leider wurde keine &Uuml;bereinstimmung mit dem Suchbegriff '.$such.' gefunden';
    }
}    

// IlchBB Forum 3.1 :: Copyright :: Start
$ilchbb_tpl->out(1);
// IlchBB Forum 3.1 :: Copryright :: End

$design->footer();

?>