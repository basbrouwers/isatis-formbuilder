<?php
/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/27/2015
 * Time: 10:42 AM
 */

class Isatis_Formbuilder_Model_Groupelement extends Mage_Core_Model_Abstract {

    protected function _construct()
    {
        parent::_construct();
        $this->_init('formbuilder/groupelement');
    }

}