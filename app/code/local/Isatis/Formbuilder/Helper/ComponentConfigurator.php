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
    const DS = DIRECTORY_SEPARATOR;


    public function __construct()
    {
        $this->template = file_get_contents(Mage::getBaseDir('design') . DS . 'adminhtml' . DS . 'default' . DS . 'default' . DS . 'template' . DS . 'formbuilder' . DS . 'elements.phtml');
        $this->dom = new domDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($this->template);
        $this->xPath = new DOMXPath($this->dom);
    }

    /**
     * Function deteermines what type of field should be configured
     * @param $fieldData array
     * @return string
     */
    public function configureField($fieldData)
    {

        switch ($fieldData['type']) {
            case 'text':
                return $this->configureTextField($fieldData);
                break;
            case 'select':
                return $this->configureSelectField($fieldData);
                break;
            case 'textarea':
                return $this->configureTextareaField($fieldData);
                break;
            case 'radio':
                return $this->configureRadiobuttonField($fieldData);
                break;
            case 'checkbox':
                return $this->configureCheckboxField($fieldData);
                break;

        }
    }


    /**
     * @param $fieldData array containing all data needed for configuring field
     * @return string
     */
    public function configureFieldset($fieldData)
    {

        $fieldSet = $this->xPath->evaluate('//fieldset')->item(0);

        $fieldSet->removeChild($this->dom->getElementsByTagName('legend')->item(0));

        $fieldSet->appendChild($this->dom->createElement('legend', $fieldData['legend']));
        $this->dom->saveHTML();

        foreach ($fieldData['elements'] as $element) {
            $this->configureField($element);
        }
        return $this->dom->saveHTML();
    }


    /**
     * @param $fieldData array
     */
    public function configureTextField($fieldData)
    {

        echo $fieldData['name'];
        /**
         * @var $newNode DOMNodeList
         */
        $newNode = $this->dom->getElementsByTagName('input')->item(0)->cloneNode();
        $newNode->nodeValue = $fieldData['value'];
        $this->dom->getElementsByTagName('fieldset')->item(0)->appendChild($newNode);
        $this->dom->saveHTML();
    }

    public function configureSelectField($fieldData)
    {
        $selectBox = ' <select name="' . $fieldData['name'] . '">';
        $selectBox .= $this->getSelectOptions($fieldData['options']);
        $selectBox .= '</select>';
        return $selectBox;
    }


    public function configureTextareaField($fieldData)
    {
        return '<label for="' . $fieldData['name'] . $fieldData['element_id'] . '">' . $fieldData['label'] . 'zxcv</label><textarea name="' . $fieldData['name'] . '"></textarea><br />';
    }

    public function configureRadiobuttonField($fieldData)
    {

    }

    public function configureCheckboxField($fieldData)
    {

    }

    private function getSelectOptions($options)
    {
        $code = '';
        foreach ($options as $option) {
            $code .= '<option value="' . $option['value'] . '">' . $option['value'] . '</option>';
        }

        return $code;
    }

}