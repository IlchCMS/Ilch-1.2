<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2012 ilch.de
 * @version $Id
 */

/* Als Beispieldatei wenn ihr noch etwas über die update.php ausführen müsst/wollt ;)


db_query("INSERT INTO `prefix_credits` (
				`id` ,
				`sys` ,
				`name` ,
				`version` ,
				`url` ,
				`lizenzname` ,
				`lizenzurl`
			)
				VALUES 
			(
				NULL , 
				'gfx', 
				'ilch-Design', 
				'1.0', 
				'http://ilch.de', 
				'by W@rLord and coded by Tigereyes', 
				'http://www.gnu.de/gpl-ger.html'
			);	
");

ALT: $rev ist SVN Revisionnummer NEU: $rev ist ein einmaliger key, also z.B. branchname_1
$rev='239';
$update_messages[$rev][] = 'Credits erweitert';

*/