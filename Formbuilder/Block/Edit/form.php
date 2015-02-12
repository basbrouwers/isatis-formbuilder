<?php
/**
 * Created by PhpStorm.
 * User: basb
 * Date: 2/11/2015
 * Time: 10:46 AM
 */
class Isatis_Formbuilder_Block_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'isatis_formbuilder';
        $this->_controller = 'edit';
        $this->_headerText = "Isatis Formbuilder";

    }
    /**
     * Preparing form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', array(
            'legend' => 'legend',
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('label', 'text', array(
            'name' => 'label',
            'label' => 'label',
        ));

        if (Mage::registry('Formbuilder_edit')) {
            $form->setValues(Mage::registry('Formbuilder_form')->getData());
        }
        return parent::_prepareForm();
    }
}