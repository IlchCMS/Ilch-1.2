<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );

$design = new design( 'Ilch Admin-Control-Panel :: Eigene Box/Page', '', 2 );

// func einbinden
$funcs = read_ext( 'include/admin/inc/selfbp', 'php' );

foreach ( $funcs as $file ) {
    require_once( 'include/admin/inc/selfbp/' . $file );
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
// l�schen
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