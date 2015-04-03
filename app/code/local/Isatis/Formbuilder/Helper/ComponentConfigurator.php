<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 3/3/2015
 * Time: 12:58 PM
 */
class Isatis_Formbuilder_Helper_ComponentConfigurator extends Mage_Core_Helper_Abstract
{
    var $template = '';
    var $dom = '';
    var $xPath = '';
    var $currentPage = 1;
    var $currentFieldset = '';
    var $validationResult = false;
    var $postData = false;
    const DS = DIRECTORY_SEPARATOR;

    public function __construct()
    {
        //load the template file containing the form elements
        $this->template = file_get_contents(Mage::getBaseDir('design') . DS . 'adminhtml' . DS . 'base' . DS . 'default' . DS . 'template' . DS . 'formbuilder' . DS . 'elements.phtml');

        $this->dom = new domDocument();
        libxml_use_internal_errors(true);

        /**@todo update error handling when domDocument can't load template file */
        try {
            $this->dom->loadHTML($this->template);
        } catch (Exception $e) {
            die($e->getMessage());
        }

    }

    /**
     * @param $result array
     */
    public function setValidationResult($result){
        $this->validationResult = $result;
    }

    /**
     * @param $fieldsetData array containing all data needed for configuring field
     * @return string
     */
    public function configureFieldset($fieldsetData, $postData=false)
    {
        $this->currentFieldset = $fieldsetData['legend'];
        /**
         * @var $newNode DOMNodeList
         */
        $newFieldset = new domDocument();

        //clone the template element so we have a unique element to work with
        $wrappingDiv = $this->dom->getElementById('fieldsetTemplate')->cloneNode(true);

        //remove id so we don't get double id's in html
        $wrappingDiv->removeAttribute('id');

        //update the legend of the fieldset
        //$legend is a reference to legend dom element inside $wrappingDiv.
        $wrappingDiv->getElementsByTagName('legend')->item(0)->nodeValue = $fieldsetData['legend'];

        //loop through the form elements belonging to the fieldset and append them
        foreach ($fieldsetData['elements'] as $fieldsetName =>$element) {

            $elementCode = $this->configureField($element, $fieldsetName);

            if (isset($element['childElements'])) {

                $childElementCode = '';
                foreach ($element['childElements'] as $childElement) {
                    $childElementCode = $this->appendChild($childElement);

                    if ($childElement['parentdependency'] == 1) {
                        $childElementCode->setAttribute('class', 'dependent');
                    }

                    $elementCode->appendChild($childElementCode);

                    if ($childElement['parentdependency'] == 1) {
                        $elementCode->setAttribute('class', 'dependencyTrigger');
                    }

                    if(isset($postData[$fieldsetName][$childElement['name'].'-'.$childElement['element_id']])){
                        die('FOUND');
                    }
                }
            }

            if ($elementCode->hasAttribute('id')) {
                $elementCode->removeAttribute('id');
            }

            $wrappingDiv->getElementsByTagName('fieldset')->item(0)->appendChild($elementCode);
        }

        //append the elments by importing the node $wrappingDiv and appending
        $newFieldset->appendChild($newFieldset->importNode($wrappingDiv, true));

        $this->xPath = new DOMXPath($newFieldset);

        //remove the sortable divs from the code
        $sortables = $this->xPath->query("//div[contains(concat(' ', @class, ' '), 'sortable')]");
        /**
         * @var $e DOMElement
         */
        foreach ($sortables as $e) {
            $e->parentNode->removeChild($e);
        }

        //return the generated html code by saving the newFieldsset
        return $newFieldset->saveHTML($newFieldset->getElementsByTagName('fieldset')->item(0));
    }


    /**
     * @param $childElement
     * @return string
     */
    private function appendChild($childElement) {

        $elementCode = $this->configureField($childElement);
        if(isset($childElement['childElements'])){
            foreach($childElement['childElements'] as $subChild){
                $elementCode->appendChild($this->appendChild($subChild));
            }
        }

        return $elementCode;
    }


    /**
     * Function deteermines what type of field should be configured
     * @param $fieldData array
     * @param $fieldsetName
     * @return string
     */
    public function configureField($fieldData, $fieldsetName=false)
    {

        switch ($fieldData['type']) {
            case 'text':
                return $this->configureTextField($fieldData,$fieldsetName);
                break;
            case 'select':
                return $this->configureSelectField($fieldData,$fieldsetName);
                break;
            case 'textarea':
                return $this->configureTextareaField($fieldData,$fieldsetName);
                break;
            case 'radio':
                return $this->configureRadiobuttonField($fieldData,$fieldsetName);
                break;
            case 'checkbox':
                return $this->configureCheckboxField($fieldData,$fieldsetName);
                break;
            case 'label':
                $wrappingDiv = $this->dom->getElementById('labelFieldTemplate')->cloneNode(true);
                $this->configureLabel($fieldData, $wrappingDiv);
                return $wrappingDiv;
                break;
            case 'date':
                return $this->configureDateField($fieldData,$fieldsetName);
                break;
            case 'infobox':
                return $this->configureInfobox($fieldData,$fieldsetName);
                break;
            case 'yes-no':
                return $this->configureYesNo($fieldData,$fieldsetName);
                break;
            case 'group':
                return $this->configureGroup($fieldData,$fieldsetName);
                break;
            case 'customhtml':
                return $this->configureCustomHTML($fieldData,$fieldsetName);
                break;
        }
    }


    /**
     * @param $fieldData array
     * @return DomElement node
     */
    private function configureGroup($fieldData)
    {
        $wrappingDiv = $this->dom->getElementById('groupFieldTemplate')->cloneNode(true);

        $wrappingDiv->removeChild($wrappingDiv->getElementsByTagName('div')->item(0));
        $this->configureLabel($fieldData, $wrappingDiv);
        $list = $this->dom->createElement('ul');
        $list->setAttribute('class', 'childplacement');
        $wrappingDiv->appendChild($list);

        return $wrappingDiv;
    }


    /**
     * @param $fieldData
     * @return DOMNode
     */
    public function configureTextField($fieldData)
    {
        /**
         * @var $newNode DOMNodeList
         */
        //clone the template element so we have a unique element to work with
        $wrappingDiv = $this->dom->getElementById('textFieldTemplate')->cloneNode(true);

        //configure the input field
        $textInput = $wrappingDiv->getElementsByTagName('input')->item(0);
        $textInput->setAttribute('id', 'element-' . $fieldData['element_id']);
        $textInput->setAttribute('value', $fieldData['value']);
        $textInput->setAttribute('name', $this->currentFieldset . '[' . $fieldData['name'] .'-'.$fieldData['element_id']. ']');
        if($fieldData['validationrule']!='') {
            $textInput->setAttribute('class',$textInput->getAttribute('class').' '. $fieldData['validationrule']);
        }
        if($fieldData['required']==1) {
            $textInput->setAttribute('class',$textInput->getAttribute('class').' '. 'required-entry');
        }
        if($fieldData['placeholder']!='') {
            $textInput->setAttribute('placeholder',$fieldData['placeholder']);
        }
        if(isset($this->validationResult[$fieldData['element_id']])) {
            $textInput->setAttribute('class',$textInput->getAttribute('class').' '. 'error');
        }
        //configure the label.
        $this->configureLabel($fieldData, $wrappingDiv);

        return $wrappingDiv;
    }


    /**
     * @param $fieldData
     * @return DOMNode
     */
    public function configureSelectField($fieldData)
    {
        /**
         * @var $newNode DOMNodeList
         */
        //clone the template element so we have a unique element to work with
        $wrappingDiv = $this->dom->getElementById('selectFieldTemplate')->cloneNode(true);
        //remove the placeholder option
        $select = $wrappingDiv->getElementsByTagName('select')->item(0);

        

        $select->removeChild($wrappingDiv->getElementsByTagName('option')->item(0));

        //configure the selectbox
        $selectbox = $wrappingDiv->getElementsByTagName('select')->item(0);
        $selectbox->setAttribute('id', 'element-' . $fieldData['element_id']);
        $selectbox->setAttribute('name', '' . $this->currentFieldset . '[' . $fieldData['name'] .'-'.$fieldData['element_id'].  ']');

        $this->configureLabel($fieldData, $wrappingDiv);
        //add the options to the selectbox
        foreach ($fieldData['options'] as $option) {
            $newOption = $this->dom->createElement('option');
            $newOption->appendChild($this->dom->createTextNode($option['label'])); /*Text node is what the user will see*/

            $newOption->setAttribute('value', $option['value']);


            $selectbox->appendChild($newOption);

        }
        return $wrappingDiv;
    }


    /**
     * @param $fieldData
     * @return DOMNode
     */
    public function configureTextareaField($fieldData)
    {
        /**
         * @var $newNode DOMNodeList
         */
        //clone the template element so we have a unique element to work with
        $wrappingDiv = $this->dom->getElementById('textareaFieldTemplate')->cloneNode(true);

        //configure the input field
        $textarea = $wrappingDiv->getElementsByTagName('textarea')->item(0);
        $textarea->setAttribute('id', 'element-' . $fieldData['element_id']);
        $textarea->setAttribute('value', $fieldData['value']);
        $textarea->setAttribute('name', '' . $this->currentFieldset . '[' . $fieldData['name'] .'-'.$fieldData['element_id'].  ']');
        if($fieldData['validationrule']!='') {
            $textarea->setAttribute('class',$textarea->getAttribute('class').' '. $fieldData['validationrule']);
        }
        if($fieldData['required']==1) {
            $textarea->setAttribute('class',$textarea->getAttribute('class').' '. 'required-entry');
        }
        //configure the label.
        $this->configureLabel($fieldData, $wrappingDiv);
        return $wrappingDiv;
    }


    /**
     * @param $fieldData
     * @return DOMNode
     */
    public function configureRadiobuttonField($fieldData)
    {
        $wrappingDiv = $this->dom->getElementById('radioFieldTemplate')->cloneNode(true);
        $wrappingDiv->removeAttribute('id');
        //configure the input field
        $textInput = $wrappingDiv->getElementsByTagName('input')->item(0);
        $textInput->setAttribute('id', 'element-' . $fieldData['element_id']);
        $textInput->setAttribute('value', $fieldData['value']);
        $textInput->setAttribute('name', '' . $this->currentFieldset . '[' . $fieldData['name'] .']');
        if($fieldData['validationrule']!='') {
            $textInput->setAttribute('class', $fieldData['validationrule']);
        }

        //configure the label.
        $this->configureLabel($fieldData, $wrappingDiv);
        return $wrappingDiv;
    }


    /**
     * @param $fieldData array
     * @return DOMNode
     */
    public function configureCheckboxField($fieldData)
    {
        $wrappingDiv = $this->dom->getElementById('checkboxFieldTemplate')->cloneNode(true);
        $checkbox = $wrappingDiv->getElementsByTagName('input')->item(0);
        $checkbox->setAttribute('id', 'element-' . $fieldData['element_id']);
        $checkbox->setAttribute('value', $fieldData['value']);
        $checkbox->setAttribute('name', '' . $this->currentFieldset . '[' . $fieldData['name'] .'-'.$fieldData['element_id'].  ']');
        if($fieldData['required']==1) {
            $checkbox->setAttribute('class',$checkbox->getAttribute('class').' '. 'required-entry');
        }
        //configure the label.
        $this->configureLabel($fieldData, $wrappingDiv);
        return $wrappingDiv;
    }

    /**
     * @param $options array
     * @return string
     */
    private function getSelectOptions($options)
    {
        $code = '';
        foreach ($options as $option) {
            $code .= '<option value="' . $option['value'] . '">' . $option['value'] . '</option>';
        }

        return $code;
    }


    /**
     * @param $fieldData array
     * @param $wrappingDiv DOMNode
     */
    private function configureLabel($fieldData, $wrappingDiv)
    {
        $label = $wrappingDiv->getElementsByTagName('label')->item(0);
        $label->setAttribute('for', 'element-' . $fieldData['element_id']);
        if($fieldData['required']) {
            $label->setAttribute('class', 'required-entry');
        }
        $label->nodeValue = $fieldData['label'];

    }


    /**
     * @param $fieldData
     * @return DOMNode
     */
    private function configureYesNo($fieldData)
    {
        $yesNoElement = $this->dom->getElementById('yes-noFieldTemplate')->cloneNode(true);

        $newLabel = $this->dom->createElement('label');
        $labelText = $this->dom->createTextNode($fieldData['label']);
        $newLabel->appendChild($labelText);

        $oldLabel = $yesNoElement->getElementsByTagName('label')->item(0);
        $yesNoElement->replaceChild($newLabel, $oldLabel);

        foreach ($yesNoElement->getElementsByTagName('input') as $key => $input) {
            $input->setAttribute('name', $this->currentFieldset . '[' . $fieldData['name'] .'-'.$fieldData['element_id'].  ']');
        }

        return $yesNoElement;
    }


    /**
     * @param $fieldData array
     * @return DOMNode
     */
    private function configureDateField($fieldData)
    {
        $wrappingDiv = $this->dom->getElementById('dateFieldTemplate')->cloneNode(true);
        $dropdowns = $wrappingDiv->getElementsByTagName('select');

        foreach ($dropdowns as $dropdown) {
            $dropdown->setAttribute('name', $this->currentFieldset . '[' . $fieldData['name'] . '][' . $dropdown->getAttribute('name') .'-'.$fieldData['element_id'].  ']');
        }

        //configure the label.
        $this->configureLabel($fieldData, $wrappingDiv);
        return $wrappingDiv;
    }


    /**
     * Function configures a textBox element containing a h4 tag and a div with text
     * @param $fieldData array
     * @return DOMNode
     */
    private function configureInfoBox($fieldData)
    {

        $wrappingDiv = $this->dom->getElementById('infoboxFieldTemplate')->cloneNode(true);


        $titleContainer = $wrappingDiv->firstChild->cloneNode();

        //remove the default title en text from the template
        while ($wrappingDiv->hasChildNodes()) {
            $wrappingDiv->removeChild($wrappingDiv->firstChild);
        }

        //create new h3 element
        $titleContainer = $this->dom->createElement('h3');
        //create textNode
        $title = $this->dom->createTextNode($fieldData['label']);
        //append the textnode to the new titleContainer element
        $titleContainer->appendChild($title);


        //repeat for text in the infoBox
        $textContainer = $this->dom->createElement('div');
        $text = $this->dom->createTextNode($fieldData['value']);
        $textContainer->appendChild($text);

        //append the new elements to the wrappingDiv
        $wrappingDiv->appendChild($titleContainer);
        $wrappingDiv->appendChild($textContainer);

        return $wrappingDiv;
    }

    private function configureCustomHTML($fieldData)
    {
        $wrappingDiv = $this->dom->getElementById('customhtmlFieldTemplate')->cloneNode(true);

        //remove the label because it is only needed in the editor
        $label = $wrappingDiv->getElementsByTagName('label')->item(0);
        $wrappingDiv->removeChild($label);

        //create a new fragment from the string stored in the fieldData containing our custom HTML code
        $customHTML = $this->dom->createDocumentFragment();
        $customHTML->appendXML($fieldData['value']);

        //append the newly created dom element to the wrappingDiv
        $wrappingDiv->appendChild($customHTML);

        return $wrappingDiv;
    }


}