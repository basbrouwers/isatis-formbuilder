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
        /**
         * @var $form Isatis_Formbuilder_Model_Form
         */
        $form = Mage::getModel('formbuilder/form');

        //check if there is a form id present. If so, load the form and update
        if (isset($post['form_id']) && $post['form_id'] != '') {
            $form->load($post['form_id']);
        }

        $form->setTitle($post['form_title']);
        $form->setTemplate($post['form_template']);
        $form->setSubtemplate($post['form_subtemplate']);

        $form->save();
        $post['form_id'] = $form->getId();

        $jsonData = Mage::helper('core')->jsonEncode($post);
        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
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
        if (isset($post['parent_id']) && $post['parent_id'] != '') {
            //check what kind of element we need to save. If it is a fieldset
            //then we forward to a different function
            if (isset($post['element_type']) && strtolower($post['element_type']) == 'fieldset') {
                $this->saveFieldsetAction($post);
            } else {
                $post = $this->saveFormElementAction($post);
            }
        } else {
            $post['error'] = 'Please save the form before adding elements';
        }

        $jsonData = Mage::helper('core')->jsonEncode($post);
        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     * Fetches the id from string $value. id is always proceeded by an '_' sign
     * @param $value string (called by reference)
     */
    public function getCleanId(&$value)
    {
        preg_match('/^.*_(.*?)$/', $value, $matches);
        $value = $matches[1];
    }

    /**
     *
     */
    public function saveSortOrderAction()
    {

        /** @var  $post array */
        $post = $this->getRequest()->getPost();

        /** @var $sortOrder array */
        $sortOrder = array();

        if (isset($post['sortOrder']) && $post['sortOrder'] != '') {
            $sortOrder = array_filter(explode(',', $post['sortOrder']));
            array_walk($sortOrder, array($this, 'getCleanId'));
            $sortOrder = array_flip($sortOrder);
        }

        /**
         * @var $elementModel Isatis_Formbuilder_Model_Element
         */
        $elements = Mage::getModel('formbuilder/element')->getCollection()->addFieldToFilter('element_id', array('in' => array_keys($sortOrder)));

        foreach ($elements as $element) {
            $element->setSortOrder($sortOrder[$element->getElementId()]);
            try {
                $element->save();
            } catch (Exception $e) {
                $post['error'] = true;
                $post['message'] = $e->getMessage();
                break;
            }
        }


        $jsonData = Mage::helper('core')->jsonEncode($post);
        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     * Function collects all data for specified form and outputs json string
     */
    public function editFormAction()
    {
        $this->loadLayout()->renderLayout();
    }


    /**
     * @param $post array containing the fieldset data
     * @throws Exception
     */
    private function saveFieldsetAction(&$post)
    {
        $fieldset = Mage::getModel('formbuilder/fieldset');

        if (isset($post['element_id'])) {
            //load the element so we can update it
            $fieldset->load($post['element_id']);
        } else {
            //first time we save the element so set crdate in table
            $fieldset->setCrdate(time());
        }
        $fieldset->setFormId($post['parent_id']);
        $fieldset->setLegend($post['element_legend']);
        $fieldset->setTstamp(time());
        $fieldset->save();
        $post['element_id'] = $fieldset->getId();

    }

    /**
     * @param $post
     * @return mixed
     * @throws Exception
     */
    public function saveFormElementAction($post)
    {
        if (isset($post['parent_id']) && $post['parent_id'] != '') {
            /**
             * @var $element isatis_formbuilder_model_element
             */
            $element = Mage::getModel('formbuilder/element');
            if (isset($post['element_id'])) {
                //load the element so we can update it
                $element->load($post['element_id']);
            } else {
                //first time we save the element so set crdate in table
                $element->setCrdate(time());
            }

            $element->setFieldset_id($post['parent_id']);
            $element->setName($post['element_name']);
            $element->setLabel($post['element_label']);
            $element->setValue($post['element_value']);
            $element->setType($post['element_type']);
            $element->setTstamp(time());
            $element->save();

            //return the id of the element
            $post['element_id'] = $element->getId();

            //check if we need to save additional fields
            if ($post['element_type'] == 'select') {
                //save the options for the selectbox
                $optionModel = Mage::getModel('formbuilder/option');
                foreach ($post['option'] as $optionData) {
                    $optionModel->setElement_id($element->getId());
                    $optionModel->setValue($optionData);
                    $optionModel->setTstamp(time());
                    $optionModel->save();
                    $optionModel->unsetData();
                }
                return $post;
            }
            return $post;
        } else {
            $post['error'] = 'Please save the form before adding elements';
            return $post;
        }
    }

    /**
     * Deletes a form element from the database
     */
    public function removeElementAction()
    {
        $post = $this->getRequest()->getPost();

        if (!isset($post['element_id']) || $post['element_id'] == '') {
            $post['error'] = true;
            $post['message'] = "Element NOT removed! Element not found in database";
        } else {
            $elementModel = Mage::getModel('formbuilder/element');
            try {
                $elementModel->setId($post['element_id'])->delete();
            } catch (Exception $e) {
                $post['error'] = true;
                $post['message'] = "Element NOT removed! Error when executing query";
            }
        }

        $jsonData = Mage::helper('core')->jsonEncode($post);

        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     * @return array
     */
    public function getFormDataAction()
    {
        $post = Mage::app()->getRequest()->getPost();
        $formId = $post['requested_form_id'];

        $form = Mage::getModel('formbuilder/form')->getCollection()->addFieldToFilter('form_id', $formId)->getData();

        //Instanciate fieldset Model and get all fieldset for the requested form
        $fieldsets = Mage::getModel('formbuilder/fieldset')->getCollection()->addFieldToFilter('form_id', $formId)->setOrder('tstamp', 'desc')->getData();


        foreach ($fieldsets as &$fieldset) {
            $elements = Mage::getModel('formbuilder/element')->getCollection()->addFieldToFilter('fieldset_id', $fieldset['fieldset_id'])->setOrder('sort_order', 'desc')->getData();
            //add the elements to the fieldset
            $fieldset['elements'] = $elements;
        }

        //add the fieldset to the form
        $form[0]['fieldsets'] = $fieldsets;

        $jsonData = Mage::helper('core')->jsonEncode($form);

        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }
}