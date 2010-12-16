<?php
# www.ilch.de
# Author: T0P0LIN0                                                     
# thanks to uwe slick! http://www.deruwe.de/captcha.html - his thoughts

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
?>