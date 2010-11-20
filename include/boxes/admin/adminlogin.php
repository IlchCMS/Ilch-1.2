<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

echo '<li><a href="admin.php"><span>Eingeloggt als: <strong>' . $_SESSION[ 'authname' ] . '</strong></span></a></li>' . '<li><a href="index.php"><span><strong>Startseite</strong></span></a></li>' . '<li><a href="index.php?user-3"><span><strong>Logout</strong></span></a></li>';