<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

// DEVELOPERS PLEASE NOTE
//
// Some characters you may want to copy&paste:
// ’ » „ “ — …

$lang = array_merge($lang, array(
    //Regist in Joinus
    'registwithoutconfirm' => "Hallo <b>%s</b>,<br /><br />deine Anmeldung war erfolgreich!<br /><br />Bitte hebe dein Passwort \"<b>%s</b>\" gut auf, da es nur verschlüsselt in der Datenbank gespeichert und Dir nicht per E-Mail zugesendet wurde. Du kannst dich nun <a href=\"index.php?user-2\">einloggen</a>.<br /><br />Mit freundlichen Grüßen<br />Administrator",
    'registconfirmbyadmin' => "Hallo <b>%s</b>,<br /><br />deine Anmeldung wurde erfolgreich hinterlegt, ein Administrator wird in Kürze deinen Account freischalten!<br />Bitte hebe dein Passwort \"<b>%s</b>\" gut auf, da es nur verschlüsselt in der Datenbank gespeichert wurde.<br /><br />Du kannst dich erst nach der Freischaltung <a href=\"index.php?user-2\">einloggen</a>.<br /><br />Mit freundlichen Grüßen<br />Administrator",
    'registconfirmbylink' => "Hallo <b>%s</b>,<br /><br />deine Anmeldung war erfolgreich!<br /><br />Dir wurde eine E-Mail mit den Zugangsdaten und dem Aktivierungslink zugesendet.<br />Bitte hebe dein Passwort gut auf, da es nur verschlüsselt in der Datenbank gespeichert wurde.<br /><br />Nachdem du den Aktivierungslink der E-Mail bestätigt hast, kannst du dich <a href=\"index.php?user-2\">einloggen</a>.<br /><br />Mit freundlichen Grüßen<br />Administrator"
));
