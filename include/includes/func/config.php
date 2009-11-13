<?php
function parse_type( $attr_name, $type, $value )
{
    $part = explode( ' ', trim( $type ) );
    // break; wird nich gebraucht... immerhin hab ich return ;-)
    switch ( strtolower( $part[ 0 ] ) ) {
        
        case 'radio':
            $r = '';
            for ( $i = 0; isset( $part[ $i + 1 ] ); $i++ )
                $r .= '<input type="radio" name="' . $attr_name . '" value="' . $i . '"' . ( $i == $value ? 'checked="checked"' : '' ) . '/>' . $part[ $i + 1 ] . "<br/>\n";
            return $r;
        
        case 'textarea':
            $cols = ( is_numeric( $part[ 1 ] ) ? $part[ 1 ] : 30 );
            $rows = ( is_numeric( $part[ 2 ] ) ? $part[ 2 ] : 3 );
            return '<textarea name="' . $attr_name . '" cols="' . $cols . '" rows="' . $rows . '">' . $value . '</textarea>';
        
        case 'password':
            return '<input type="password" value="******" name="' . $attr_name . '" maxlength="255" size="50"/>';
        
        case 'dir':
            $dirs  = scandir( './' . $part[ 1 ] );
            $files = array( );
            
            $r = '<select name="' . $attr_name . '">';
            switch ( strtolower( $part[ 2 ] ) ) { // use filter?
                
                case 'contains':
                    foreach ( $dirs as $d )
                        if ( $d[ 0 ] != '.' || !is_file( $part[ 1 ] . $d ) || file_exists( $part[ 1 ] . $d . '/' . $part[ 3 ] ) )
                            $r .= '<option value="' . $d . '"' . ( $d == $value ? ' selected="selected"' : '' ) . '>' . $d . '</option>';
                    break;
                
                case 'match':
                    foreach ( $dirs as $d )
                        if ( preg_match( '%' . $part[ 3 ] . '%', $d ) )
                            $r .= '<option value="' . $d . '"' . ( $d == $value ? ' selected="selected"' : '' ) . '>' . $d . '</option>';
                    break;
                
                default:
                    foreach ( $dirs as $d )
                        if ( $d[ 0 ] != '.' )
                            $r .= '<option value="' . $d . '"' . ( $d == $value ? ' selected="selected"' : '' ) . '>' . $d . '</option>';
                    break;
            }
            return $r . '</select>';
        
        case 'text': // text = default
        default:
            return '<input type="text" value="' . $value . '" name="' . $attr_name . '" size="35"/>';
    }
}
?>