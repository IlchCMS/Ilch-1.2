<?php

	/* -----> Rev. 132 <----- */
			@db_query("ALTER TABLE `prefix_user` ADD `sperre` TINYINT( 1 ) NOT NULL AFTER `status`");
			echo '<p><strong>Hier tritt nach einer neu-installation warscheinlich ein "duplicate-entry" fehler auf der ignoriert werden kann</strong></p><br />';

?>