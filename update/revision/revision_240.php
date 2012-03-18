<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
 */

db_query("INSERT INTO `prefix_config` (
`schl` ,
`typ` ,
`typextra` ,
`kat` ,
`frage` ,
`wert` ,
`pos` ,
`hide` ,
`helptext`
)
VALUES (
'jqueryui', 's', NULL , 'Allgemeine Optionen', 'jQueryUI-Design', 'redmond', '0', '0', 'Zum Ändern des jQuery-Designs lese die FAQ unter <br />https://github.com/IlchCMS/Ilch-1.2/wiki/Doc-jquery_ui'
);


			);	
");

$rev='240';
$update_messages[$rev][] = 'jQuery UI Theme Auswahl';
