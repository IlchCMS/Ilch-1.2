<?php
// Copyright by Manuel
// Support www.ilch.de
$tpl_alianz = <<< tpl
<div align="center">
{EXPLODE}
</div>
{EXPLODE}
<a class="box" href="{link}" target="_blank">{title}</a><br />
{EXPLODE}
<img src="{banner}" alt="{name}" border="0" />
tpl;

defined('main') or die('no direct access');

$allyAnzahl = $allgAr[ 'Aanz' ];
if ($allgAr[ 'Aart' ] == 1) {
    $sqlORDER = '`pos`';
} else {
    $sqlORDER = 'RAND()';
}

$allyNameAr = array();
$allyLinkAr = array();
$allyBanaAr = array();
$allyAktAnz = 0;

$allyAbf = 'SELECT * FROM `prefix_partners` ORDER BY ' . $sqlORDER . ' LIMIT  0,' . $allyAnzahl;
$allyErg = db_query($allyAbf);
if (db_num_rows($allyErg) > 0) {
    $tpl = new tpl($tpl_alianz, 3);
    $tpl->out(0);
    while ($allyRow = db_fetch_object($allyErg)) {
        $tpl->set("link", $allyRow->link);
        if (empty($allyRow->banner) OR $allyRow->banner == 'http://') {
            $tpl->set("title", $allyRow->name);
        } else {
            $tpl->set("title", $tpl->set_ar_get(array(
                        "banner" => $allyRow->banner,
                        "name" => $allyRow->name
                        ), 3 // {EXPLODE} Nr 3
                    ));
        }
        $tpl->out(2);
    }
    $tpl->out(1);
}

?>