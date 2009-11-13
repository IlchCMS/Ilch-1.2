<?php
//   Copyright by Thomas Bowe [Funjoy]
//	Modul nur für die bbcode.php Klasse. 
//   Support webmaster@phpline.de
//   link www.k4d-clan.com

//> CSS farblich darstellen.
function highlight_css( $string, $stag = NULL, $etag = NULL )
{
    //> Array mit den Farbinhalten.
    $parse_elements = array( //> Farbe für die Kommentare.
         "#999999",
        //> farbe für den Klassennamen.
        "#ff00ff",
        //> Allgemeine Farbe für die Funktionen.
        "#000099",
        //> Formatierungen farbe
        "#0000ff" 
    );
    
    
    //> farben inerhalb des Codes erstmal löschen.
    $pattern[ ] = "%<font(.*)>%siU";
    $replace[ ] = "";
    
    $pattern[ ] = "%<\/font>%siU";
    $replace[ ] = "";
    
    //> CSS Funktionen einfärben.
    $pattern[ ] = "%(\{)(.*)(\})%siU";
    $replace[ ] = "$1<font color=\"" . $parse_elements[ 2 ] . "\">$2</font>$3";
    
    //> Doppelpunkt oder Semikolon einfärben;
    $pattern[ ] = "%:([A-Za-z0-9\-_ #,]*);%siU";
    $replace[ ] = "<font color=\"" . $parse_elements[ 1 ] . "\">:</font><font color=\"" . $parse_elements[ 3 ] . "\">$1</font><font color=\"" . $parse_elements[ 1 ] . "\">;</font>";
    
    //> Kommentare einfärben
    $pattern[ ] = "%\/\*(.*)\*\/%esiU";
    $replace[ ] = "_css_comments('\$1','" . $parse_elements[ 0 ] . "')";
    
    //> Einleitungs- Tags hervorheben.
    $pattern[ ] = "%(&lt;!--|--&gt;)%siU";
    $replace[ ] = "<font color=\"" . $parse_elements[ 0 ] . "\">$1</font>";
    
    $string = preg_replace( $pattern, $replace, $string );
    
    if ( is_null( $stag ) && is_null( $etag ) ) {
        return "<font color=\"" . $parse_elements[ 1 ] . "\">" . stripslashes( $string ) . "</font>";
    } else {
        return $stag . "<font color=\"" . $parse_elements[ 1 ] . "\">" . $string . "</font>" . $etag;
    }
    
}

//> Sub Funktion um Kommentare farblich hervorzuheben.
function _css_comments( $string, $color )
{
    //> Farb- Tags (<font>) und Formatierungs- Tags (<b><i>) Löschen.
    $pattern = array(
         "%<font(.*)>%siU",
        "%</font>%siU",
        "%<(i|b)>%siU",
        "%</(i|b)>%siU" 
    );
    
    $string = preg_replace( $pattern, "", $string );
    
    return "<font color=\"" . $color . "\">/*" . $string . "*/</font>";
}
?>