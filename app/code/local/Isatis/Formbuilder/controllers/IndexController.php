<?php
/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/26/2015
 * Time: 4:07 PM
 */

class Isatis_Formbuilder_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
        $this->loadLayout()->renderLayout();
    }

    /**
     * [publishFormAction description]
     * @return [type] [description]
     */
    public function publishFormAction()
    {
         $this->loadLayout()->renderLayout();
    }

}