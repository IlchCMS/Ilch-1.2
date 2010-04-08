<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );


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