<?php
/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/23/2015
 * Time: 3:01 PM
 */

class Isatis_Formbuilder_Block_Adminhtml_Form_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('id');
        $this->setId('isatis_formbuilder_form_grid');
        $this->setDefaultDir('asc');
    }

}