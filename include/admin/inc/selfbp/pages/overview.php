<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design->header();

$tpl = new tpl('selfbp/overview', 1);
// zuerst die seiten
$tpl->out("overview pages start");

$pages = read_ext("include/contents/selfbp/selfp", "php");

if (sizeof($pages) > 0) {
    foreach($pages as $page) {
        $properties = get_properties(get_text("p" . $page));
        $tpl->set("filename", $page);
        $tpl->set_ar_out($properties, "overview pages_item");
    }
} else {
    $tpl->out("overview no pages");
}

$tpl->out("overview pages end");
// end of pages
// dann die boxen
$tpl->out("overview boxes start");

$boxes = read_ext("include/contents/selfbp/selfb", "php");

if (sizeof($boxes) > 0) {
    foreach($boxes as $box) {
        $properties = get_properties(get_text("b" . $box));
        $tpl->set("filename", $box);
        $tpl->set_ar_out($properties, "overview boxes_item");
    }
} else {
    $tpl->out("overview no boxes");
}

$tpl->out("overview boxes end");
// end of boxes
// buttons für die aktionen
$tpl->out("overview actions");

$design->footer();