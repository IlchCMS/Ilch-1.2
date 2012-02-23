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

    /**
     * Constructor
     * Setzt einige Optionen, damit man sich nicht um Optionen von Smarty kümmern muss
     */
    public function __construct() {
        parent::__construct();
        $this->left_delimiter = '{';
        $this->right_delimiter = '}';
        if (defined('admin')) {
            $this->addTemplateDir('include/admin/templates');
        } else {
            $this->addTemplateDir(array(
                'include/designs/' . tpl::get_design() . '/templates',
                'include/templates'
            ));
        }
        $this->setCompileDir('include/cache/smarty_compile');
        $this->addPluginsDir('include/includes/libs/smarty/plugins');
    }
}