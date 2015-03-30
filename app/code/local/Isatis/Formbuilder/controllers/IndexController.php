<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/26/2015
 * Time: 4:07 PM
 */
class Isatis_Formbuilder_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
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

    public function postFormAction()
    {
        $post = $this->getRequest()->getPost();
        $validator = Mage::helper('formbuilder/Validator');

        $data = '';
        foreach ($post as $fieldsetKey => $fieldset) {
            if ($fieldsetKey != 'form_key' && $fieldsetKey != 'submit' && $fieldsetKey != 'form_id') {
                $data .= '<h3>' . $fieldsetKey . '</h3>';
                foreach ($fieldset as $name => $value) {
                    if (is_array($value)) {
                        $data .= '<strong>' . $name . '</strong><br />';
                        foreach ($value as $key => $val) {
                            $data .= '&nbsp;&nbsp;&nbsp;&nbsp;' . $key . ': ' . $val . '<br />';
                        }
                    } else {
                        $data .= '<strong>' . $name . ':</strong>' . $value . '<br />';
                    }
                }
            }
        }
        echo $data;
    }
}