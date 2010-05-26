<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

function server_parse($socket, $response, $line = __LINE__) {
    $server_response = '';
    while (substr($server_response, 3, 1) != ' ') {
        if (!($server_response = fgets($socket, 256))) {
            echo 'Couldn\'t get mail server response codes<br />';
        }
    }

    if (!(substr($server_response, 0, 3) == $response)) {
        echo "Ran into problems sending Mail. Response: " . $server_response . "<br />";
    }
}

function smtpmail($mail_to, $subject, $message, $headers = '') {
    global $allgAr;
    $smtp_host = $allgAr[ 'mail_smtp_host' ];
    $smtp_username = $allgAr[ 'mail_smtp_login' ];
    require_once('include/includes/class/AzDGCrypt.class.inc.php');
    $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
    $smtp_password = $cr64->decrypt($allgAr[ 'mail_smtp_password' ]);

    $absender = $allgAr[ 'mail_smtp_email' ];

    $message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);
    if ($headers != '') {
        if (is_array($headers)) {
            if (sizeof($headers) > 1) {
                $headers = join("\n", $headers);
            } else {
                $headers = $headers[ 0 ];
            }
        }
        $headers = chop($headers);
        $headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);
        $header_array = explode("\r\n", $headers);
        @reset($header_array);

        $headers = '';
        while (list(, $header) = each($header_array)) {
            if (preg_match('#^cc:#si', $header)) {
                $cc = preg_replace('#^cc:(.*)#si', '\1', $header);
            } else if (preg_match('#^bcc:#si', $header)) {
                $bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
                $header = '';
            }
            $headers .= ($header != '') ? $header . "\r\n" : '';
        }

        $headers = chop($headers);
        $cc = explode(', ', $cc);
        $bcc = explode(', ', $bcc);
    }

    if (trim($subject) == '') {
        echo 'No email Subject specified<br />';
    }

    if (trim($message) == '') {
        echo 'Email message was blank<br />';
    }

    if (!$socket = @fsockopen($smtp_host, 25, $errno, $errstr, 20)) {
        echo "Could not connect to smtp host : $errno : $errstr<br />";
    }

    server_parse($socket, "220", __LINE__);

    if (!empty($smtp_username) && !empty($smtp_password)) {
        fputs($socket, "HELO " . $smtp_host . "\r\n");
        server_parse($socket, "250", __LINE__);

        fputs($socket, "AUTH LOGIN\r\n");
        server_parse($socket, "334", __LINE__);

        fputs($socket, base64_encode($smtp_username) . "\r\n");
        server_parse($socket, "334", __LINE__);

        fputs($socket, base64_encode($smtp_password) . "\r\n");
        server_parse($socket, "235", __LINE__);
    } else {
        fputs($socket, "HELO " . $smtp_host . "\r\n");
        server_parse($socket, "250", __LINE__);
    }

    fputs($socket, "MAIL FROM: <" . $absender . ">\r\n");
    server_parse($socket, "250", __LINE__);

    $to_header = '';

    $mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);
    if (preg_match('#[^ ]+\@[^ ]+#', $mail_to)) {
        fputs($socket, "RCPT TO: <$mail_to>\r\n");
        server_parse($socket, "250", __LINE__);
    }

    @reset($bcc);
    if (isset($bcc)) {
        while (list(, $bcc_address) = each($bcc)) {
            $bcc_address = trim($bcc_address);
            if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address)) {
                fputs($socket, "RCPT TO: <$bcc_address>\r\n");
                server_parse($socket, "250", __LINE__);
            }
        }
    }

    @reset($cc);
    if (isset($cc)) {
        while (list(, $cc_address) = each($cc)) {
            $cc_address = trim($cc_address);
            if (preg_match('#[^ ]+\@[^ ]+#', $cc_address)) {
                fputs($socket, "RCPT TO: <$cc_address>\r\n");
                server_parse($socket, "250", __LINE__);
            }
        }
    }

    fputs($socket, "DATA\r\n");

    server_parse($socket, "354", __LINE__);

    fputs($socket, "Subject: $subject\r\n");

    fputs($socket, "To: $mail_to\r\n");

    fputs($socket, "$headers\r\n\r\n");

    fputs($socket, "$message\r\n");

    fputs($socket, ".\r\n");
    server_parse($socket, "250", __LINE__);

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return true;
}

?>