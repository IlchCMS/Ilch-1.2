<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
// liest die <!--@..=..@--> in den ersten 1024 Zeichen in ein Array aus
function get_properties( $file )
{
    $handle = fopen( $file, 'r' );
    $buffer = fread( $handle, 1024 );
    fclose( $handle );
    preg_match_all( "|(?:<!--@(?P<name>[^=]*)=(?P<value>.*)@-->)|U", $buffer, $out, PREG_SET_ORDER );
    unset( $buffer );
    
    $properties = array( );
    foreach ( $out as $x ) {
        $properties[ $x[ 'name' ] ] = $x[ 'value' ];
    }
    unset( $out );
    return $properties;
}

if ( $menu->get( 1 ) != '' ) {
    // moegliche endungen
    $ende_ar = array(
         '.html',
        '.htm',
        '.php' 
    );
    $um      = $menu->get( 1 );
    // um ../ backlinks in unterordner kicken.
    $um      = str_replace( '../', '', $um );
    $um      = str_replace( './', '', $um );
    
    foreach ( $ende_ar as $ext ) {
        $file = 'include/contents/selfbp/selfp/' . $menu->get( 1 ) . $ext;
        if ( file_exists( $file ) ) {
            $properties = get_properties( $file );
            
            if ( $properties[ 'view' ] == "fullscreen" ) {
                require_once( $file );
            } elseif ( $properties[ 'view' ] == "popup" ) {
                if ( $menu->get( 2 ) != 'true' ) {
                    $title  = $allgAr[ 'title' ] . ' :: ' . $properties[ 'title' ];
                    $hmenu  = $properties[ 'hmenu' ];
                    $design = new design( $title, $hmenu );
                    $design->header();
                    
?>
          <script language="JavaScript" type="text/javascript">
          <!--
          var fenster = window.open('index.php?self-<?php
                    echo $menu->get( 1 );
?>-true','Seite','<?php
                    echo $properties[ 'viewoptions' ];
?>');
          fenster.focus();
           -->
          </script>
<?php
                    echo '<a href="index.php?self-' . $menu->get( 1 ) . '-true">' . $properties[ 'title' ] . '</a>';
                    
                    $design->footer();
                } else {
                    require_once( $file );
                }
            } else {
                $title  = $allgAr[ 'title' ] . ' :: ' . $properties[ 'title' ];
                $hmenu  = $properties[ 'hmenu' ];
                $design = new design( $title, $hmenu );
                $design->header();
                
                require_once( $file );
                
                $design->footer();
            }
            $ok = true;
            break;
        }
    }
}

if ( $ok != true ) {
    // dieser teil hier muss auch in die eigene self datei eingefuehgt werden.
    // die datei muss aber die endung .php haben!!! und dann einfach den teil hier
    // einfueghen und zwar bis zum #ENDE DESIGN
    // und dann noch ganz am ende der self_ datei $design->footer();
    // allers natuerlich in den php bereich der seite.
    $title  = $allgAr[ 'title' ];
    $hmenu  = "";
    $design = new design( $title, $hmenu );
    $design->header();
    // ENDE DESIGN
    // das muss auch in die self datei eingefueght werden wenn sie direkt aufgerufen
    // werden soll, davor aber auch noch das header ding am anfang ;9...
    $design->footer();
}

?>