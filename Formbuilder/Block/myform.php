<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/22/2015
 * Time: 2:29 PM
 */
class Isatis_Formbuilder_Block_Myform extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllForms()
    {
        return Mage::getModel('formbuilder/form')->getCollection();

    }
}