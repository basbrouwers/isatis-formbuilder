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
        $form->setSubtemplate($post['form_subtemplate']);
        $form->setReceiver($post['form_receiver']);
        $form->save();

        $post['form_id'] = $form->getId();
        $post['message'] = $this->__('Form saved succesfully');

        $jsonData = Mage::helper('core')->jsonEncode($post);
        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     * [publishFormAction description]
     * @return [type] [description]
     */
    public function publishFormAction()
    {
        $this->loadLayout()->renderLayout();
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
                if ($post['parent_type'] == 'radiogroup') {
                    $post = $this->saveGroupElement($post);

                } else {
                    $post = $this->saveFormElementAction($post);
                }
            }
        } else {
            $post['error'] = true;
            $post['message'] = 'Please save the form before adding elements';
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
        $start = strpos($value, '_') + 1;
        $value = substr($value, intval($start));
        $result = $value;
        return $result;
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


        /** @var $element Isatis_Formbuilder_Model_Element */
        $element = Mage::getModel('formbuilder/element');

        foreach ($sortOrder as $key => $value) {

            try {
                $element->load($key)->setSortOrder($value)->save();


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


    public function saveGroupElement($post)
    {
        $groupElement = Mage::getModel('formbuilder/groupelement');
        $parent_id = substr($post['parent_id'], strpos($post['parent_id'], '_') + 1);
        if (isset($post['element_id']) && $post['element_id'] != '') {
            preg_match('/element[_-]?(\d)^/', $post['element_id'], $match);
            $elementId = $match[1];
            //load the element so we can update it
            $groupElement->load($elementId);
        } else {
            //first time we save the element so set crdate in table
            $groupElement->setCrdate(time());
        }

        $groupElement->setElementId($parent_id);
        $groupElement->setLabel($post['element_label']);
        $groupElement->setValue($post['element_value']);
        $groupElement->setSortorder($post['element_sort_order']);
        $groupElement->setType($post['element_type']);
        $groupElement->setTstamp(time());
        $groupElement->save();
        $post['element_id'] = $groupElement->getId();

        return $post;
    }

    /**
     * @param $post array containing the fieldset data
     * @throws Exception
     */
    private function saveFieldsetAction(&$post)
    {
        $fieldset = Mage::getModel('formbuilder/fieldset');

        if (isset($post['element_id']) && $post['element_id'] != '') {
            preg_match('/element[_-]?(\d)^/', $post['element_id'], $match);
            $fieldsetId = $match[1];
            //load the element so we can update it
            $fieldset->load($fieldsetId);
        } else {
            //first time we save the element so set crdate in table
            $fieldset->setCrdate(time());
        }

        $fieldset->setFormId($post['parent_id']);
        $fieldset->setLegend($post['element_legend']);
        $fieldset->setPagenumber($post['pagenumber']);
        $fieldset->setColumn($post['column']);
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

            $parent_id = substr($post['parent_id'], strpos($post['parent_id'], '_') + 1);
            /**
             * @var $element isatis_formbuilder_model_element
             */

            $element = Mage::getModel('formbuilder/element');
            if (isset($post['element_id']) && $post['element_id'] != '') {
                preg_match('/[_-](\d*)/si', $post['element_id'], $matches);
                $elementId = $matches[1];
                //load the element so we can update it
                $element->load($elementId);
            } else {
                //first time we save the element so set crdate in table
                $element->setCrdate(time());
            }
            //check if we are storing a child element. If so, set the fieldsetId to that of the parent element
            $fieldset_id = 0;
            if (isset($post['parent_type']) && $post['parent_type'] != 'fieldset') {
                //get the fieldset id of the parent
                $parent_element = Mage::getModel('formbuilder/element');
                $parent_element->load($parent_id);
                $fieldset_id = $parent_element->getFieldsetId();
            } else {
                $fieldset_id = $parent_id;
            }


            $element->setFieldsetId($fieldset_id);
            $element->setParentId($parent_id);
            $element->setName($post['element_name']);
            $element->setLabel($post['element_label']);
            $element->setValue($post['element_value']);
            $element->setType($post['element_type']);
            $element->setRequired($post['element_required']);
            $element->setValidationrule($post['element_validationrule']);
            $element->setPlaceholder($post['element_placeholder']);
            $element->setSortorder($post['element_sort_order']);
            $element->setTstamp(time());
            if(isset($post['element_parentdependency'])) {
            $element->setParentdependency(1);
                } else {
                $element->setParentdependency(0);
            }

            $element->save();

            //return the id of the element
            $post['element_id'] = $element->getId();

            //check if we need to save additional fields

            if ($post['element_type'] == 'select') {
                //save the options for the selectbox
                $optionModel = Mage::getModel('formbuilder/option');

                
                foreach ($post['option'] as $option_id=>$optionData) {
                    if ($option_id) {
                        $optionModel->load($this->getCleanId($option_id));
                    }
                        $optionModel->setElementId($element->getId());

                    if (strpos($optionData, '|')) {
                        $optionData = explode('|', $optionData);
                        $optionModel->setLabel($optionData[0]);
                        $optionModel->setValue($optionData[1]);
                    } else {
                        $optionModel->setValue($optionData);
                    }


                    $optionModel->setTstamp(time());
                    $optionModel->save();
                    $optionModel->unsetData();
                }
                return $post;
            }
            return $post;
        } else {
            $post['error'] = true;
            $post['message'] = 'Please save the form before adding elements';
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
            $this->getCleanId($post['element_id']);
            $element_id = $post['element_id'];

            try {
                $elementModel->setId($element_id)->delete();
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
     */
    public function getFormDataAction()
    {
        $post = Mage::app()->getRequest()->getPost();
        $formId = $post['requested_form_id'];

        $form = $this->getData($formId);

        $jsonData = Mage::helper('core')->jsonEncode($form);

        /** @var $this Isatis_Formbuilder_Adminhtml_IndexController */
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type: application/json')
            ->setBody($jsonData);
    }

    /**
     * @param $formId integer
     *
     * @return array
     */
    public function getData($formId)
    {
        $form = Mage::getModel('formbuilder/form')->getCollection()->addFieldToFilter('form_id', $formId)->getData();

        //Instanciate fieldset Model and get all fieldset for the requested form
        $fieldsets = Mage::getModel('formbuilder/fieldset')->getCollection()->addFieldToFilter('form_id', $formId)->setOrder('sort_order', 'desc')->getData();

        foreach ($fieldsets as &$fieldset) {
            $elements = Mage::getModel('formbuilder/element')
                ->getCollection()
                ->addFieldToFilter('fieldset_id', $fieldset['fieldset_id'])
                ->setOrder('sort_order', 'desc')
                ->getData();


            //add options to selectbox elements
            foreach ($elements as $key => $element) {
                if ($element['type'] == 'select') {
                    $options = Mage::getModel('formbuilder/option')->getCollection()->addFieldToFilter('element_id', $element['element_id'])->setOrder('sort_order', 'asc')->getData();
                    $elements[$key]['options'] = $options;
                }
                if ($element['type'] == 'group') {
                    //fetch the associated group elements
                    $groupElements = Mage::getModel('formbuilder/groupelement')
                        ->getCollection()
                        ->addFieldToFilter('element_id', $element['element_id'])
                        ->setOrder('sort_order', 'asc')->getData();
                    $elements[$key]['groupElements'] = $groupElements;
                }

            }
            //add the elements to the fieldset
            $fieldset['elements'] = $elements;
        }
        //add the fieldset to the form
        $form[0]['fieldsets'] = $fieldsets;
        return $form;
    }


    public function postFormAction()
    {

        $post = $this->getRequest()->getPost();

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
                        $data .= '<strong>'.$name . ':</strong>' . $value . '<br />';
                    }
                }
            }
        }
        echo $data;
    }

}