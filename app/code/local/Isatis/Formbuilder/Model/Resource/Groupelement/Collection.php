<?php
/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/28/2015
 * Time: 9:28 AM
 */

class Isatis_Formbuilder_Model_Resource_Groupelement_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
{
    parent::_construct();
    $this->_init('formbuilder/groupelement');
}
}