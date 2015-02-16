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
        return $form =  Mage::getModel('formbuilder/form')->getCollection();
    }


    public function getActiveForm(){
        $post = Mage::app()->getRequest()->getPost();
        $form_id = false;


        if(isset($post['selected_form_id']) && $post['selected_form_id']!='') {
            $form_id = $post['selected_form_id'];
        }

        return $form_id;
    }



}