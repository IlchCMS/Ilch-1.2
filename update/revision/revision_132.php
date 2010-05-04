<?php

	/* -----> Rev. 132 <----- */
			db_query("ALTER TABLE `prefix_user` ADD `sperre` TINYINT( 1 ) NOT NULL AFTER `status`");

?>