<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/28/2015
 * Time: 9:26 AM
 */
class Isatis_Formbuilder_Model_Mysql4_Form extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('formbuilder/form', 'form_id');
    }
}