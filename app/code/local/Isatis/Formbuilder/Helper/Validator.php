<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/27/2015
 * Time: 11:53 AM
 */
class Isatis_Formbuilder_Helper_Validator extends Mage_Core_Helper_Abstract
{
    var $validationRules = array(
        'required-entry'=>"/.+/",
        'required-file'=>"",
        'validate-admin-password'=>"",
        'validate-ajax'=>"",
        'validate-alpha'=>"/\d+/",
        'validate-alphanum'=>"/\w+/",
        'validate-alphanum-with-spaces'=>"/[\s\w]+/",
        'validate-both-passwords'=>"",
        'validate-cc-cvn'=>"",
        'validate-cc-exp'=>"",
        'validate-cc-number'=>"",
        'validate-cc-type'=>"",
        'validate-cc-type-select'=>"",
        'validate-cc-ukss'=>"",
        'validate-clean-url'=>"",
        'validate-code'=>"",
        'validate-cpassword'=>"",
        'validate-css-length'=>"",
        'validate-currency-dollar'=>"",
        'validate-custom'=>"",
        'validate-date-month-nl'=>"/(?:(0?[1-9]|1[012]) | (?:Januari|Februari|Maart|April|Mei|Juni|July|Augustus|September|O[c|k]tober)|November|December)/",
        'validate-date-day-nl'=>"",
        'validate-date-year-nl'=>"",
        'validate-data'=>"",
        'validate-date'=>"",
        'validate-date-au'=>"",
        'validate-date-range'=>"",
        'validate-digits'=>"",
        'validate-digits-range'=>"",
        'validate-email'=>"/^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+)*@([a-z0-9-]|)+(\.([a-z0-9-]|)+)*\.(([a-z]|){2,})$/i",
        'validate-emailSender'=>"/^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-])+)*@([a-z0-9-]|)+(\.([a-z0-9-]|)+)*\.(([a-z]|){2,})$/i",
        'validate-fax'=>"",
        'validate-greater-than-zero'=>"/^[1-9]+$/",
        'validate-identifier'=>"",
        'validate-length'=>"",
        'validate-new-password'=>"",
        'validate-no-html-tags'=>"/^((?!</?[a-z]+>).*)$/",
        'validate-not-negative-number'=>"",
        'validate-number'=>"",
        'validate-number-range'=>"",
        'validate-one-required'=>"",
        'validate-one-required-by-name'=>"",
        'validate-password'=>"",
        'validate-percents'=>"",
        'validate-phoneLax'=>"",
        'validate-phoneStrict'=>"",
        'validate-select'=>"/.*/",
        'validate-ssn'=>"",
        'validate-state'=>"",
        'validate-street'=>"",
        'validate-url'=>"",
        'validate-xml-identifier'=>"",
        'validate-zero-or-greater'=>"/^[0-9]+$/",
        'validate-zip'=>"/[1-9]/",
        'validate-zip-international'=>"");


    var $postData = '';
    var $objForm = '';
    var $validationResults = array();
    var $elementKeys = array();
    var $elements = array();

    public function validateForm()
    {
        $this->postData = $this->_getRequest()->getPost();
        //see what elements are stored in the submitted form and get their id's
        //store them in $this->elementKeys
        $this->extractElementIds('',$this->postData);

        //fetch the elements from the database based on the extracted elementId's
        $this->elements = Mage::getModel('formbuilder/element')->getCollection()->addFieldToFilter('element_id', array('in',$this->elementKeys))->getData();

        //transpose the elements array so the element_id= key
        foreach($this->elements as $key=>$element) {
            $this->elements[$element['element_id']]  = $element;
            unset($this->elements[$key]);
        }
        foreach ($this->postData as $key=>$value) {
            $this->validateElement($key, $value);
        }

        return $this->validationResults;
    }

    /**
     * @param $key string
     * @param $data string|array
     */
    private function extractElementIds($key,$data)
    {
        if (!is_array($data)) {
            $elementID = array_pop(explode('-', $key));
            if(is_numeric($elementID)) {
                $this->elementKeys[]=$elementID;
            }
        } else {
            foreach ($data as $elementKey => $elementData) {
                $this->extractElementIds($elementKey,$elementData);
            }
        }
    }

    /**
     * @param $key string
     * @param $data string|array
     */
    private function validateElement($key, $data)
    {
        if (!is_array($data)) {

            if(strpos($key,'-')) {


                $element_id = array_pop(explode('-', $key));
                if(isset($this->validationRules[$this->elements[$element_id]['validationrule']])) {
                    $validationRule = $this->validationRules[$this->elements[$element_id]['validationrule']];
                    if($validationRule!='') {
                        $result = preg_match($validationRule, $data);
                    } else {
                        $result=1;
                    }

                    if ($result === 0) {
                        $this->validationResults[$element_id][$data] = $result;
                    }
                }
            }
        } else {
            foreach ($data as $elementKey => $elementData) {
                $this->validateElement($elementKey, $elementData);
            }
        }

    }

}

