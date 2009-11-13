<?php
// Copyright by Manuel
// Support www.ilch.de
defined( 'main' ) or die( 'no direct access' );

if ( empty( $_GET[ 'sum' ] ) ) {
    $heute = date( 'Y-m-d' );
    
    $ges_visits  = db_result( db_query( "SELECT SUM(`count`) FROM `prefix_counter`" ), 0 );
    $ges_heute   = @db_result( db_query( "SELECT `count` FROM `prefix_counter` WHERE `date` = '" . $heute . "'" ), 0 );
    $ges_gestern = @db_result( db_query( 'SELECT `count` FROM `prefix_counter` WHERE `date` < "' . $heute . '" ORDER BY `date` DESC LIMIT 1' ), 0 );
    
    echo $lang[ 'whole' ] . ': ' . $ges_visits . '<br />';
    echo $lang[ 'today' ] . ': ' . $ges_heute . '<br />';
    echo $lang[ 'yesterday' ] . ': ' . $ges_gestern . '<br />';
    echo 'Online: ' . ges_online() . '<br />';
    echo '<a class="box" href="index.php?statistik"><b>... ' . $lang[ 'more' ] . '</b></a>';
} else {
    $title  = $allgAr[ 'title' ] . ' :: Statistik';
    $hmenu  = 'Statistik';
    $design = new design( $title, $hmenu, 0 );
    $design->header();
    
    $anzahlShownTage = 7;
    
    echo '<br /><table width=90%" align="center" class="border" cellpadding="0" cellspacing="1" border="0"><tr><td>';
    echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">';
    echo '<tr class="Chead"><td colspan="3" align="center"><b>Site Statistik</b></td></tr>';
    
    $max_in    = 0;
    $ges       = 0;
    $dat       = array( );
    $max_width = 200;
    
    $maxErg = db_query( 'SELECT MAX(count) FROM `prefix_counter`' );
    $max_in = db_result( $maxErg, 0 );
    
    $erg = db_query( "SELECT `count`, DATE_FORMAT(`date`,'%a der %d. %b') as `datum` FROM `prefix_counter` ORDER BY `date` DESC LIMIT " . $anzahlShownTage );
    while ( $row = db_fetch_row( $erg ) ) {
        $value = $row[ 0 ];
        
        if ( empty( $value ) ) {
            $bwidth = 0;
        } else {
            $bwidth = $value / $max_in * $max_width;
            $bwidth = round( $bwidth, 0 );
        }
        
        echo '<tr class="Cnorm">';
        echo '<td>' . $row[ 1 ] . '</td>';
        echo '<td><table width="' . $bwidth . '" border="0" cellpadding="0" cellspacing="0">';
        echo '<tr><td height="2" class="border"></td></tr></table>';
        echo '</td><td align="right">' . $value . '</td></tr>';
        
        $ges += $value;
    }
    
    $gesBesucher = db_query( 'SELECT SUM(count) FROM prefix_counter' );
    $gesBesucher = @db_result( $gesBesucher, 0 );
    
    echo '<tr class="Cmite"><td colspan="3"><div align="right">';
    echo 'Wochen Summe: ' . $ges . '</div>';
    echo 'Besucher Gesamt ' . $gesBesucher . ' &nbsp; Maximal ' . $max_in . '<br /><br />';
    echo '</td></tr><tr class="Cdark">';
    echo '<td colspan="3" align="center">[ <a href="javascript:window.close()">Fenster Schliesen</a> ]</td>';
    echo '</tr></table></td></tr></table><br />';
    
    $design->footer();
}

?>