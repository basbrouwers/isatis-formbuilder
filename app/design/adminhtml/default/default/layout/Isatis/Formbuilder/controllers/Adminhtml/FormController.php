<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 2/11/2015
 * Time: 9:52 AM
 */
class Isatis_Formbuilder_Adminhtml_FormController extends Mage_Adminhtml_Controller_Action
{


        public function indexAction()
    {
        // instantiate the grid container
        $brandBlock = $this->getLayout()
            ->createBlock('Isatis_Formbuilder_Edit/Form');

        // add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($brandBlock)
            ->renderLayout();
        $this->loadLayout()->renderLayout();
    }


    public function newFormAction()
    {
        die("New form");
    }

    public function editFormAction()
    {
        die("edit form");
    }
}