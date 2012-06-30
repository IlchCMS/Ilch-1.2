<?php
# www.ilch.de
# Author: T0P0LIN0
# thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts


//Wenn die Variante mit den Textdateien zur Speicherung benutzt werden soll,
//muss die Datei existieren und Schreibrechte haben bzw. der Ordner in dem sie erstellt werden soll
class Memory
{
    var $file = 'txt/numbers.txt';
    var $path_file = 'include/includes/libs/captcha/txt/numbers.txt';

    function numberExists( $paramNumber )
    {
        $lines = file( $this->path_file );
        foreach ( $lines as $line ) {
            list( $number, $date, $timestamp, $ip ) = explode( '#', $line );
            if ( $number == $paramNumber ) {
                $this->updateMemory();
                return true;
            }
        }
        return false;
    }

    function updateMemory( )
    {
        $newData = array( );
        $lines   = file( $this->path_file );
        foreach ( $lines as $line ) {
            list( $number, $date, $timestamp, $ip ) = explode( '#', $line );

            if ( $timestamp >= ( time() - 120 ) ) {
                $newData[ ] = $line;
            } else {
            }
        }
        $handle = fopen( $this->path_file, "w" );
        foreach ( $newData as $line ) {
            fwrite( $handle, $line );
        }
    }

    function saveNumber( $number, $handle = null )
    {
        if ( !$handle ) {
            $handle = fopen( $this->file, "a+" );
        }
        fwrite( $handle, $number . '#' . date( 'd.m.y h:i:s' ) . '#' . time() . '#' . $_SERVER['REMOTE_ADDR'] . "\n" );
    }
}


/**
 * Memory_Session
 * Speichern der Zahlen für den Antispam in der Session
 *
 * @author Mairu
 * @version $Id$
 */
class Memory_Session
{
    public function __construct() {
        session_name('sid');
        session_start();
        if (!isset($_SESSION['antispam_numbers'])) {
            $_SESSION['antispam_numbers'] = array();
        } else {
            $this->updateMemory();
        }
    }

    function numberExists( $paramNumber )
    {
        if (array_key_exists($paramNumber, $_SESSION['antispam_numbers'])) {
            unset($_SESSION['antispam_numbers'][$paramNumber]);
            return true;
        }
        return false;
    }

    function updateMemory( )
    {
        $tooOld = time() - 60 * 30; //30 Minuten
        foreach ($_SESSION['antispam_numbers'] as $number => $time) {
            if ($tooOld > $time) {
                unset($_SESSION['antispam_numbers'][$number]);
            }
        }
    }

    function saveNumber( $number, $handle = null )
    {
        $_SESSION['antispam_numbers'][$number] = time();
    }
}
?>