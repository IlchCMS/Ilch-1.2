<?php

	/* -----> Rev. 132 <----- */
			@db_query("ALTER TABLE `prefix_user` ADD `sperre` TINYINT( 1 ) NOT NULL AFTER `status`");
      $rev='132';
      $update_messages[$rev][] = 'Usertabelle um das feld sperre erweitert';