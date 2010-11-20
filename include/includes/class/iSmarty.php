<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
require 'include/includes/libs/smarty/Smarty.class.php';

/**
 * iSmarty
 *
 * @author Mairu
 * @copyright Copyright (c) 2010
 * @version $Id$
 * @access public
 */
class iSmarty extends Smarty {
    //Speichert das Design
    private $chosenDesign;

    /**
     * Constructor
     * Setzt einige Optionen, damit man sich nicht um Optionen von Smarty kümmern muss
     */
    public function __construct() {
        $this->left_delimiter = '{';
        $this->right_delimiter = '}';
        if (defined('admin')) {
            $this->template_dir = 'include/admin/templates';
        } else {
            $this->template_dir = 'include/templates';
        }
        $this->template_dir = 'include/templates';
        $this->compile_dir = 'include/cache/smarty_compile';
        $this->chosenDesign = tpl::get_design();
    }

    /**
     * iSmarty::fetch()
     * Wrapper für die fetch Funktion vom Smarty, um im Designordner nach Templates zu suchen
     *
     * @param string $template the resource handle of the template file or template object
     * @param mixed $cache_id cache id to be used with this template
     * @param mixed $compile_id compile id to be used with this template
     * @param object $ |null $parent next higher level of Smarty variables
     * @return string rendered template output
     */
    public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false) {
        if (is_string($template) and file_exists('include/designs/' . $this->chosenDesign . '/templates/' . $template)) {
            $changeback = true;
            $this->template_dir = 'include/designs/' . $this->chosenDesign . '/templates';
        } else {
            $changeback = false;
        }
        // display template
        $return = parent::fetch ($template, $cache_id, $compile_id, $parent, true);
        if ($changeback) {
            $this->template_dir = 'include/templates';
        }
        return $return;
    }
}