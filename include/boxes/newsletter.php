<?php
// Copyright by Manuel
// Support www.ilch.de
defined('main') or die('no direct access');

if (empty($_POST[ 'NEWSLETTER' ])) {?>

  <form action="index.php" method="post">

		<b>Newsletter</b>
	  <br />
		<input type="text" name="NEWSLETTER" size="15" />
		<br />
		<br />
		<input type="submit" style="width:120px; height:20px;" value="<?php
    echo $lang[ 'newsletterinout' ];

    ?>" />

	</form>


<?php

} else {
    $email = escape($_POST[ 'NEWSLETTER' ], 'string');
    $erg = db_query("SELECT COUNT(*) FROM `prefix_newsletter` WHERE `email` = '" . $email . "'");
    $anz = db_result($erg, 0);
    if ($anz == 1) {
        db_query("DELETE FROM `prefix_newsletter` WHERE `email` = '" . $email . "'");
        echo $lang[ 'deletesuccessful' ];
    } else {
        db_query("INSERT INTO `prefix_newsletter` (`email`) VALUES ('" . $email . "')");
        echo $lang[ 'insertsuccessful' ];
    }
}

?>