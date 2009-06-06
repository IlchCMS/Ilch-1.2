<?php

session_name  ('sid');
session_start ();

$m = preg_replace("/[^a-z]+/","",$_GET['m']);
$w = intval(preg_replace("/[^0-2]/", "",$_GET['w']));
$id = $_GET['id'];

if (isset($_SESSION['antispam'][$id][$m][$w])) {
	header("Content-Type: image/jpeg");
	readfile ($_SESSION['antispam'][$id][$m][$w].'.jpg');
	/* unset($_SESSION['antispam'][$m][$w]); */
}
?>