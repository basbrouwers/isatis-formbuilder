<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 2/11/2015
 * Time: 10:46 AM
 */
class Isatis_Formbuilder_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public $formData = null;
    private $templatePath;
    private $fileName = 'elements.html';
    private $templateData;

    public function _construct()
    {
        parent::_construct();
        //open the template file
        $this->templatePath = Mage::getModuleDir('etc', 'Isatis_Formbuilder') . DIRECTORY_SEPARATOR . 'Templates';
        $this->formData = $this->getFormData();
    }




    public function buildForm()
    {
        $this->templateData = file_get_contents($this->templatePath . DIRECTORY_SEPARATOR . $this->fileName);

        $formCode = '';
        foreach($this->formData[0]['fieldsets'] as $fieldset) {
            $formCode .= $this->buildFieldset($fieldset);
        }


        return $formCode;

    }

    public function buildFieldset($fieldset)
    {
        preg_match('/<!--field.*?-->(.*?)<!--.*?-->/s',$this->templateData,$matches);
        $fieldsetCode = $matches[1];

        foreach ($fieldset['elements'] as $element) {
            $fieldsetCode .= $this->buildElement($element);
        }
        return $fieldsetCode;


    }

    private function buildElement($element)
    {
        $elementCode = '';

        //fetch the element from the templateData
        preg_match('/<!--input.*?-->(.*?)<!--.*?-->/s',$this->templateData,$matches);
        $elementCode = $matches[1];

        return $elementCode;


    }

    public function getFormfields($formId)
    {

        $formfields = Mage::getModel('formbuilder/fieldset')->getCollection()->addFieldToFilter('form_id', '7');
        return array('formfields' => $formfields);
    }
}
