<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/27/2015
 * Time: 9:42 AM
 */
class Isatis_Formbuilder_Block_Form extends Mage_Core_Block_Template
{
    var $formHierarchy = array();
    var $validationResult = false;


    private function transposeArray($dataArray)
    {
        $result = $dataArray;
        unset($result['fieldsets']);

        foreach ($dataArray['fieldsets'] as $fieldsetKey => $fieldsetArray) {
            $elementsArray = array();
            $childElements = array();
            //loop through the elements inside the fieldset
            foreach ($fieldsetArray['elements'] as $key => $element) {

                //check if the element is a child of another element
                if ($element['parent_id'] != $element['fieldset_id']) {
                    //this is a child element so we place it inside the parent
                    $childElements[$element['element_id']] = $element;
                } else {
                    foreach ($element as $elementKey => $elementValue) {
                        $elementsArray[$element['element_id']][$elementKey] = $elementValue;
                    }
                }
            }



            //add the child elements to their respective parent element
            foreach ($childElements as $childId => $childElement) {
                if (isset($childElements[$childElement['parent_id']])) {
                    $childElements[$childElement['parent_id']]['childElements'][] = $childElement;
                    unset($childElements[$childId]);
                }
            }

            foreach ($childElements as $child) {
                $elementsArray[$child['parent_id']]['childElements'][] = $child;
            }
            
            $result['fieldsets'][$fieldsetKey]=$fieldsetArray;
            $result['fieldsets'][$fieldsetKey]['elements'] = $elementsArray;

        }


        return $result;
    }

    /**
     * [publishFormAction description]
     * @return string html code of the form content, not form tag itself!
     */
    public function publishForm()
    {
        $this->validationResult = $this->getValidationData();
        
        //fetch the id of the form
        $post = Mage::app()->getRequest()->getPost();

        $form_id = null;

        if (!isset($post['form_id']) || $post['form_id'] == '') {
            throw new Exception('No form ID found');
        }

        $form_id = $post['form_id'];
        $formData = $this->getFormData($form_id);

        $formData = $this->transposeArray($formData);

        /** @var Isatis_Formbuilder_Helper_ComponentConfigurator $formConfigurator */
        $formConfigurator = Mage::helper('formbuilder/ComponentConfigurator');
        $formConfigurator->setValidationResult($this->validationResult);


        /**
         * @var $nodes array
         * $nodes is a multidimensional array. First dimension is pagenumber. Second contains the fieldsets for that page
         * place each fieldset in the correct page
         */
        $nodes = array();

        foreach ($formData['fieldsets'] as $key => $fieldset) {
            $nodes[$fieldset['pagenumber']][$fieldset['column']][] = $formConfigurator->configureFieldset($fieldset);
        }

        $formHTML = '';
        foreach ($nodes as $key => $column) {
            //$key corresponds to the current paggenumber
            $formHTML .= '<div class="formcontainer" id="page-' . $key . '">';
            $formHTML .= '<input type="hidden" name="form_id" value="' . $form_id . '">';

            foreach ($column as $colNumber => $node) {


                $formHTML .= '<div id="column' . $colNumber . '" class="col' . $colNumber;
                if($formData['subtemplate']==1){
                    $formHTML .='-full';
                }
                $formHTML .='">';
                $formHTML .= implode('', $node);
                $formHTML .= '</div>';
            };
            //close the page div
            $formHTML .= '</div>';
        }


        return $formHTML;
    }

    /**
     * @param $formId integer
     *
     * @param string $sorting
     * @return array
     */
    public function getFormData($formId, $sorting = 'asc')
    {
        $form = Mage::getModel('formbuilder/form')->getCollection()->addFieldToFilter('form_id', $formId)->getData();

        //Instanciate fieldset Model and get all fieldset for the requested form
        $fieldsets = Mage::getModel('formbuilder/fieldset')->getCollection()->addFieldToFilter('form_id', $formId)->setOrder('sort_order', $sorting)->getData();

        foreach ($fieldsets as &$fieldset) {
            $elements = Mage::getModel('formbuilder/element')->getCollection()->addFieldToFilter('fieldset_id', $fieldset['fieldset_id'])->setOrder('sort_order', $sorting)->getData();

            //add options to selectbox elements
            foreach ($elements as $key => $element) {

                if ($element['type'] == 'select') {
                    $options = Mage::getModel('formbuilder/option')->getCollection()->addFieldToFilter('element_id', $element['element_id'])->setOrder('sort_order', $sorting)->getData();
                    $elements[$key]['options'] = $options;
                }
            }

            //add the elements to the fieldset
            $fieldset['elements'] = $elements;
        }

        //add the fieldset to the form
        $form[0]['fieldsets'] = $fieldsets;


        return $form[0];
    }

    public function getFormTitle()
    {
        //fetch the id of the form
        $post = Mage::app()->getRequest()->getPost();

        
        $formId = null;
        if (isset($post['form_id']) && $post['form_id'] != '') {
            $formId = $post['form_id'];
        }
        $formTitle = Mage::getModel('formbuilder/form')->getCollection()->addFieldToFilter('form_id', $formId)->getData();

        return $formTitle[0]['title'];

    }

    private function buildHierarchy($fieldset)
    {
        $fieldsetHierarchy = array();
        foreach($fieldset['elements'] as $key=>$element) {
            if($element['parent_id']!=$element['fieldset_id']) {
                //The element is not child of the fieldset but of another element so place it under the parent element
                $fieldsetHierarchy[$element['parent_id']]['childElements'][] = $element;
            } else {
                $fieldsetHierarchy[$element['element_id']] = $element;;
            }
        }
        
        $this->formHierarchy[$fieldset['fieldset_id']] = $fieldsetHierarchy;

    }
}