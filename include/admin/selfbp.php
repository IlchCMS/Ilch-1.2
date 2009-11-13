<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );

$design = new design( 'Ilch Admin-Control-Panel :: Eigene Box/Page', '', 2 );
// liest die <!--@..=..@--> in den ersten 1024 Zeichen in ein Array aus
function get_properties( $t )
{
    preg_match_all( "|(?:<!--@(?P<name>[^=]*)=(?P<value>.*)@-->)|U", $t, $out, PREG_SET_ORDER );
    
    $properties = array( );
    foreach ( $out as $x ) {
        $properties[ $x[ name ] ] = htmlspecialchars( $x[ value ] );
    }
    unset( $out );
    return $properties;
}
// setzt die Eigenschaften zu einem String zusammen
function set_properties( $ar )
{
    $l = '';
    foreach ( $ar as $k => $v ) {
        $l .= '<!--@' . $k . '=' . $v . '@-->';
    }
    return ( $l );
}

function rteSafe( $strText )
{
    // returns safe code for preloading in the RTE
    $tmpString = $strText;
    // convert all types of single quotes
    $tmpString = str_replace( chr( 145 ), chr( 39 ), $tmpString );
    $tmpString = str_replace( chr( 146 ), chr( 39 ), $tmpString );
    $tmpString = str_replace( "'", "&#39;", $tmpString );
    // convert all types of double quotes
    $tmpString = str_replace( chr( 147 ), chr( 34 ), $tmpString );
    $tmpString = str_replace( chr( 148 ), chr( 34 ), $tmpString );
    $tmpString = str_replace( "\\\"", "\"", $tmpString );
    // replace carriage returns & line feeds
    $tmpString = str_replace( chr( 10 ), " ", $tmpString );
    $tmpString = str_replace( chr( 13 ), " ", $tmpString );
    
    return $tmpString;
}
// gibt die  options für die Dateiauswahl zurück
function get_akl( $ak )
{
    $ar_l = array( );
    
    if ( is_writeable( 'include/contents/selfbp/selfp' ) ) {
        $ar_l[ 'pneu.php' ] = 'Neue Seite';
        $o                  = opendir( 'include/contents/selfbp/selfp' );
        while ( $v = readdir( $o ) ) {
            if ( substr( $v, -4 ) != '.php' ) {
                continue;
            }
            $ar_l[ 'p' . $v ] = $v;
        }
        closedir( $o );
    }
    if ( is_writeable( 'include/contents/selfbp/selfb' ) ) {
        $ar_l[ 'bneu.php' ] = 'Neue Box';
        $o                  = opendir( 'include/contents/selfbp/selfb' );
        while ( $v = readdir( $o ) ) {
            if ( substr( $v, -4 ) != '.php' ) {
                continue;
            }
            $ar_l[ 'b' . $v ] = $v;
        }
        closedir( $o );
    }
    
    $l = '';
    foreach ( $ar_l as $k => $v ) {
        if ( $k == $ak ) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $l .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
    }
    return ( $l );
}

function get_view( $select = "normal" )
{
    $ar = array(
         'normal' => 'Normal',
        'fullscreen' => 'Vollbild',
        'popup' => 'Neues Fenster' 
    );
    $l  = '';
    foreach ( $ar as $k => $v ) {
        if ( $k == $select ) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $l .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
    }
    return ( $l );
}

function get_filename( $akl )
{
    $n = basename( substr( $akl, 1 ) );
    if ( $n == 'neu.php' or !file_exists( 'include/contents/selfbp/self' . substr( $akl, 0, 1 ) . '/' . $n ) ) {
        return '';
    }
    return ( basename( $n ) );
}
// löscht Sonderzeichen aus dem Dateinamen
function get_nametosave( $n )
{
    $n = preg_replace( "/[^a-zA-Z0-9\.]/", "", $n );
    if ( substr( $n, -4 ) != ".php" ) {
        $n .= '.php';
    }
    return ( $n );
}
// gibt den inhalt der ausgewählten Datei als String zurück
function get_text( $akl )
{
    $f = substr( $akl, 0, 1 );
    $n = basename( substr( $akl, 1 ) );
    if ( ( $f == 'b' OR $f == 'p' ) AND file_exists( 'include/contents/selfbp/self' . $f . '/' . $n ) ) {
        $t = implode( "", file( 'include/contents/selfbp/self' . $f . '/' . $n ) );
        return ( $t );
    }
    
    return ( '' );
}
// fügt defined('main') hinzu, oder entfernt es
function edit_text( $t, $add )
{
    $erg = preg_match( "/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>/s", $t );
    if ( !$erg AND $add ) {
        $t = trim( $t );
        $t = '<?php defined (\'main\') or die (\'no direct access\'); ?>' . $t;
        // $t = preg_replace("/\/([^>]*)>/","/\\1>\n",$t);
    } elseif ( $erg AND !$add ) {
        $t = preg_replace( "/^\s*<\?php defined \('main'\) or die \('no direct access'\); \?>(.*)$/s", "\\1", $t );
        $t = preg_replace( "/<!--@(.*)@-->/", "", $t );
        // $t = preg_replace ("/(\015\012|\015|\012)/", "", $t);
    }
    return ( $t );
}
// speichert die datei
function save_file_to( $filename, $data, $flags = 0, $f = false )
{
    if ( ( $f === false ) && ( ( $flags % 2 ) == 1 ) )
        $f = fopen( $filename, 'a' );
    else if ( $f === false )
        $f = fopen( $filename, 'w' );
    if ( round( $flags / 2 ) == 1 )
        while ( !flock( $f, LOCK_EX ) ) {
            /* lock */
        }
    if ( is_array( $data ) )
        $data = implode( '', $data );
    fwrite( $f, $data );
    if ( round( $flags / 2 ) == 1 )
        flock( $f, LOCK_UN );
    fclose( $f );
}

function gallery_admin_showcats( $id, $stufe )
{
    $q   = "SELECT * FROM prefix_gallery_cats WHERE cat = " . $id . " ORDER BY pos";
    $erg = db_query( $q );
    if ( db_num_rows( $erg ) > 0 ) {
        while ( $row = db_fetch_object( $erg ) ) {
            echo '<tr class="Cmite"><td>' . $stufe . '- <a href="admin.php?selfbp-imagebrowser-' . $row->id . '">' . $row->name . '</a></td></tr>';
            gallery_admin_showcats( $row->id, $stufe . ' &nbsp;' );
        }
    }
}
// gallery image browser
if ( $menu->get( 1 ) == 'imagebrowser' ) {
    $cat = 0;
    if ( $menu->get( 2 ) != '' ) {
        $cat = escape( $menu->get( 2 ), 'integer' );
    }
    
    $abf    = "SELECT `id`,`besch`,`datei_name`,`endung` FROM `prefix_gallery_imgs` WHERE `cat` = " . $cat;
    $erg    = db_query( $abf );
    $i      = 0;
    $design = new design( 'Ilch Admin-Control-Panel :: Bilder', '', 0 );
    $design->header();
    $tpl = new tpl( 'selfbp-imagebrowser', 1 );
    $tpl->out( 0 );
    gallery_admin_showcats( 0, '' );
    $tpl->out( 1 );
    while ( $row = db_fetch_assoc( $erg ) ) {
        if ( $i != 0 AND ( $i % $allgAr[ 'gallery_imgs_per_line' ] ) == 0 ) {
            echo '</tr><tr>';
        }
        $toput = 'include/images/gallery/img_' . $row[ 'id' ] . '.' . $row[ 'endung' ];
        $pfad  = 'include/images/gallery/img_thumb_' . $row[ 'id' ] . '.' . $row[ 'endung' ];
        $tpl->set( 'toput', $toput );
        $tpl->set( 'pfad', $pfad );
        $tpl->out( 2 );
        $i++;
    }
    $design->footer( 1 );
}

$f = false;
if ( !is_writable( './include/contents/selfbp/selfp' ) ) {
    $f = true;
    echo 'Das include/contents/selfbp/selfp Verzeichnis braucht chmod 777 Rechte damit du eine eigene Datei erstellen kannst!<br /><br />';
}
if ( !is_writable( './include/contents/selfbp/selfb' ) ) {
    echo 'Das include/contents/selfbp/selfb Verzeichnis braucht chmod 777 Rechte damit du eine eigene Box erstellen kannst!<br /><br />';
    if ( $f == true ) {
        exit( 'Entweder das include/contents/selfbp/selfb oder das include/contents/selfbp/selfp Verzeichnis brauchen Schreibrechte sonst kann hier nicht gearbeitet werden' );
    }
}

if ( isset( $_POST[ 'bbwy' ] ) AND isset( $_POST[ 'filename' ] ) AND isset( $_POST[ 'akl' ] ) ) {
    // speichern
    $akl  = $_POST[ 'akl' ];
    $text = $_POST[ 'bbwy' ];
    // $text = rteSafe($_POST['text']);
    $text = set_properties( array(
         'title' => $_POST[ 'title' ],
        'hmenu' => $_POST[ 'hmenu' ],
        'view' => $_POST[ 'view' ],
        'viewoptions' => $_POST[ 'viewoptions' ],
        'wysiwyg' => $_POST[ 'wysiwyg' ] 
    ) ) . $text;
    $text = edit_text( stripslashes( $text ), true );
    
    $a = substr( $akl, 0, 1 );
    // $e = substr ( $akl, 1 );
    // if ( $e != 'neu' ) {
    // unlink ( 'include/contents/selfbp/self'.$a.'/'.$e );
    // }
    if ( !empty( $_POST[ 'exfilename' ] ) AND $_POST[ 'exfilename' ] != $_POST[ 'filename' ] ) {
        $exfilename = escape( $_POST[ 'exfilename' ], 'string' );
        @unlink( 'include/contents/selfbp/self' . $a . '/' . $exfilename );
    }
    
    $filename = get_nametosave( $_POST[ 'filename' ] );
    $fname    = 'include/contents/selfbp/self' . $a . '/' . $filename;
    save_file_to( $fname, $text );
    
    if ( $_POST[ 'toggle' ] == 0 ) {
        $design->header();
        wd( 'admin.php?selfbp=0&akl=' . $a . $filename, 'Ihre Aenderungen wurden gespeichert...', 13 );
        $design->footer( 1 );
    }
}
// anzeigen
$design->header();

$tpl = new tpl( 'selfbp', 1 );
$akl = '';
if ( isset( $_REQUEST[ 'akl' ] ) ) {
    $akl = $_REQUEST[ 'akl' ];
}
// löschen
if ( isset( $_REQUEST[ 'del' ] ) ) {
    $del = $_REQUEST[ 'del' ];
    $a   = substr( $del, 0, 1 );
    $e   = substr( $del, 1 );
    
    if ( $e != 'neu' ) {
        unlink( 'include/contents/selfbp/self' . $a . '/' . $e );
    }
}

$text       = get_text( $akl );
$properties = get_properties( $text );
if ( !isset( $properties[ 'wysiwyg' ] ) ) {
    $properties[ 'wysiwyg' ] = 1;
}
$text     = edit_text( $text, false );
// $text = rteSafe($text);
$filename = get_filename( $akl );
$akl      = get_akl( $akl );
$view     = get_view( $properties[ 'view' ] );
$tpl->set_ar_out( array(
     'akl' => $akl,
    'text' => $text,
    'filename' => $filename,
    'exfilename' => $filename,
    'wysiwyg' => $properties[ 'wysiwyg' ],
    'title' => $properties[ 'title' ],
    'hmenu' => $properties[ 'hmenu' ],
    'view' => $view,
    'viewoptions' => $properties[ 'viewoptions' ],
    'wysiwyg_editor' => $properties[ 'wysiwyg' ] == 1 ? '<script type="text/javascript">buttonPath = "include/images/icons/editor/"; imageBrowse = "admin.php?selfbp-imagebrowser"; makeWhizzyWig("bbwy", "all");</script>' : '' 
), 0 );
$design->footer();

?>