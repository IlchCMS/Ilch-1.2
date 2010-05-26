<?php

/*

Magic mime file interpreter
Copyright (c) 2004 by Phillip Berndt
Version 1.0

*/

function ic_mime_type($file) {
    if (!file_exists($file)) {
        return ('application/x-object');
    }
    if (!isset($mimeData)) {
        $mimeFile = array(
            '0		string			PK\003\004		application/x-zip',
            '0	string		Rar!		application/x-rar',
            '257     string          ustar\0         application/x-tar       posix',
            '257     string          ustar\040\040\0         application/x-tar       gnu',
            '0	string		GIF		image/gif',
            '0	beshort		0xffd8		image/jpeg',
            '0	string		\137PNG			image/png',
            '>30		string	Copyright\ 1989-1990\ PKWARE\ Inc.	application/x-zip',
            '>30		string	PKLITE\ Copr.	application/x-zip'
            );
        foreach ($mimeFile as $mimeLine) {
            if ($mimeLine[ 0 ] != '#' && trim($mimeLine)) {
                if (preg_match('/^(\S+?)\s+(\S+?)\s+(\S+)(\s+(.+?))?$/si', $mimeLine, $regex_mimeDataSet)) {
                    $mimeDataSet[ 'offset' ] = str_replace('>', '', $regex_mimeDataSet[ 1 ]);
                    $indicator = $regex_mimeDataSet[ 3 ];
                    switch ($regex_mimeDataSet[ 2 ]) {
                        case 'string':
                            $indicator = str_replace('\ ', ' ', $indicator);
                            $indicator = str_replace('\<', '<', $indicator);
                            $indicator = str_replace('\>', '>', $indicator);
                            $indicator = str_replace('\r', "\r", $indicator);
                            $indicator = str_replace('\n', "\n", $indicator);
                            $indicator = preg_replace('/\\\\([0-9]{3})/e', 'chr($1);', $indicator);
                            break;
                        case 'byte':
                            $indicator = pack('c', @eval('return ' . $indicator . ';'));
                            break;
                        case 'short':
                            $indicator = pack('s', @eval('return ' . $indicator . ';'));
                            break;
                        case 'beshort':
                            $indicator = pack('n', @eval('return ' . $indicator . ';'));
                            break;
                        case 'leshort':
                            $indicator = pack('v', @eval('return ' . $indicator . ';'));
                            break;
                        case 'belong':
                            $indicator = pack('N', @eval('return ' . $indicator . ';'));
                            break;
                        case 'lelong':
                            $indicator = pack('V', @eval('return ' . $indicator . ';'));
                            break;
                        case 'long':
                            $indicator = pack('l', @eval('return ' . $indicator . ';'));
                            break;
                    }

                    $mimeDataSet[ 'indicator' ] = $indicator;
                    $mimeDataSet[ 'mime' ] = str_replace("\r", '', $regex_mimeDataSet[ 5 ]);

                    $mimeData[ ] = $mimeDataSet;
                }
            }
        }
    }
    $o = fopen($file, "r");
    $file_content = fgets($o, 4096);
    fclose($o);

    $retVal = 'application/x-object';
    foreach ($mimeData as $key => $mimeTest) {
        $testStr = substr($file_content, $mimeTest[ 'offset' ], strlen($mimeTest[ 'indicator' ]));
        if ($testStr == $mimeTest[ 'indicator' ]) {
            $mimeType = $mimeTest[ 'mime' ];
            if ($mimeType == '') {
                while ($mimeType == '') {
                    $mimeType = $mimeData[ ++$key ][ 'mime' ];
                }
            }
            $retVal = $mimeType;
        }
    }
    return $retVal;
}

?>