<?php

class Isatis_Formbuilder_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {

        $this->loadLayout()->renderLayout();
    }

    /**
     * Save form and form atributes. Update if form is already present in DB
     */
    public function saveFormAction()
    {

        $post = $this->getRequest()->getPost();
        $form = Mage::getModel('formbuilder/form');
        if(isset($post['form_id']) && $post['form_id']!='') {
            $form->load($post['form_id']);
        }

        $form->setTitle($post['form_title']);
        $form->setTemplate($post['form_template']);
        $form->setSubtemplate($post['form_subtemplate']);
        $form->save();
        $post['form_id'] = $form->getId();


        $jsonData = Mage::helper('core')->jsonEncode($post);
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     *
     */
    public function saveElementAction()
    {
        $post = $this->getRequest()->getPost();

        /**
         * @var $element isatis_formbuilder_model_element
         */
        $element = Mage::getModel('formbuilder/element');
        if(isset($post['element_id'])) {
            //load the element so we can update it
            $element->load($post['element_id']);
        } else{
            //first time we save the element so set crdate in table
            $element->setCrdate(time());
        }

        $element->setForm_id ($post['parent_id']);
        $element->setName($post['element_name']);
        $element->setLabel($post['element_label']);
        $element->setValue($post['element_value']);
        $element->setType($post['element_type']);
        $element->setTstamp(time());
        $element->save();

        //return the id of the element
        $post['element_id'] = $element->getId();

        //check if we need to save additional fields
        if($post['element_type']=='select') {
            //save the options for the selectbox
            $optionModel = Mage::getModel('formbuilder/option');
            foreach($post['option'] as $optionData) {
                $optionModel->setElement_id($element->getId());
                $optionModel->setValue($optionData);
                $optionModel->setTstamp(time());
                $optionModel->save();
                $optionModel->unsetData();
            }
        }

        $jsonData = Mage::helper('core')->jsonEncode($post);
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }
}