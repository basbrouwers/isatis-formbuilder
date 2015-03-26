<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/22/2015
 * Time: 2:29 PM
 */
class Isatis_Formbuilder_Block_Adminhtml_Form extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Isatis_Formbuilder_Model_Resource_Form_Collection
     */
    public function getAllForms()
    {
        return $form = Mage::getModel('formbuilder/form')->getCollection();
    }


    /**
     * Returns the id of the currently selected form
     * @return integer
     */
    public function getActiveForm()
    {
        $post = Mage::app()->getRequest()->getPost();
        $form_id = false;


        if (isset($post['selected_form_id']) && $post['selected_form_id'] != '') {
            $form_id = $post['selected_form_id'];
        } else {
            $form_id = $post['publish_form_id'];
        }

        return $form_id;
    }

    private function transposeArray($dataArray, $transposeKey)
    {
        $elementsArray = array();
        $childElements = array();
        foreach ($dataArray['fieldsets'] as $fieldsetKey => $fieldsetArray) {


            foreach ($fieldsetArray['elements'] as $key => $element) {

                if ($element['parent_id'] != $element['fieldset_id']) {


                    //this is a child element so we place it inside the parent
                    $childElements[$element['element_id']] = $element;
                } else {

                    foreach ($element as $elementKey => $elementValue) {
                        $elementsArray[$element['element_id']][$elementKey] = $elementValue;
                    }
                }

            }
            krsort($childElements);

            foreach($childElements as $childId=>$childElement) {
                if(isset($childElements[$childElement['parent_id']])) {
                    $childElements[$childElement['parent_id']]['childElements'][]=$childElement;
                    unset($childElements[$childId]);
                }
            }

            foreach($childElements as $child) {
                $elementsArray[$child['parent_id']]['childElements'][] = $child;
            }
            
            $dataArray['fieldsets'][$fieldsetKey]['elements'] = $elementsArray;
        }

        return $dataArray;
    }

    /**
     * [publishFormAction description]
     * @return string html code of the form content, not form tag itself!
     */
    public function publishForm()
    {
        //fetch the id of the form
        $post = Mage::app()->getRequest()->getPost();
        $form_id = null;
        if (isset($post['publish_form_id']) && $post['publish_form_id'] != '') {
            $form_id = $post['publish_form_id'];
        }

        $formData = $this->getData($form_id);
        $formData = $this->transposeArray($formData, 'element_id');


        /** @var Isatis_Formbuilder_Helper_ComponentConfigurator $formConfigurator */
        $formConfigurator = Mage::helper('formbuilder/ComponentConfigurator');

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

                $formHTML .= '<div id="column' . $colNumber . '" class="col' . $colNumber . '"">';
                $formHTML .= implode('', $node);
                $formHTML .= '</div>';
            };
            //close the page div
            $formHTML .= '</div>';
        }

        return $formHTML;
    }

    /**
     * Builds options for the days selectbox
     * @return string
     */
    public
    function fillDays()
    {
        $days = '';
        for ($i = 1; $i < 32; $i++) {
            $days .= '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
        return $days;
    }

    /**
     * Builds the select options for month selectbox
     * @return string
     */
    public
    function fillMonths()
    {
        $months = '';
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 1, $i);
            $months .= '<option value="' . $i . '">' . date("F", $timestamp) . '</option>';
        }

        return $months;
    }

    /**
     * Builds the options for the year selectbox
     * @return string
     */
    public
    function fillYears()
    {
        $currentYear = date('Y');
        $years = '';
        for ($i = $currentYear; $i > 1920; $i--) {
            $years .= '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
        return $years;
    }

    /**
     * @param $formId integer
     *
     * @param string $sorting
     * @return array
     */
    public
    function getData($formId, $sorting = 'asc')
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

    public
    function getFormTitle()
    {
        //fetch the id of the form
        $post = Mage::app()->getRequest()->getPost();
        $formId = null;
        if (isset($post['publish_form_id']) && $post['publish_form_id'] != '') {
            $formId = $post['publish_form_id'];
        }
        $formTitle = Mage::getModel('formbuilder/form')->getCollection()->addFieldToFilter('form_id', $formId)->getData();

        return $formTitle[0]['title'];

    }

}