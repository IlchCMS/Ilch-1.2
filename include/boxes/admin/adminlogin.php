<?php
// Copyright by Manuel
// Support www.ilch.de
defined( 'main' ) or die( 'no direct access' );

echo '<li><a href="admin.php"><span>Eingeloggt als: <strong>' . $_SESSION[ 'authname' ] . '</strong></span></a></li>' . '<li><a href="index.php"><span><strong>Startseite</strong></span></a></li>' . '<li><a href="index.php?user-3"><span><strong>Logout</strong></span></a></li>';

?>
