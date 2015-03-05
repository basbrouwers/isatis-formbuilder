<?php

/**
 * Created by PhpStorm.
 * User: basb
 * Date: 1/22/2015
 * Time: 2:29 PM
 */
class Isatis_Formbuilder_Block_Myform extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

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
        }

        return $form_id;
    }

    /**
     * Builds options for the days selectbox
     * @return string
     */
    public function fillDays()
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
    public function fillMonths()
    {
        $months = '';
        for ($i=1; $i<=12; $i++)
        {
            $timestamp = mktime(0,0,1,$i);
            $months .= '<option value="'.$i.'">'.date("F", $timestamp).'</option>';
        }

        return $months;
    }

    /**
     * Builds the options for the year selectbox
     * @return string
     */
    public function fillYears()
    {
        $currentYear = date('Y');
        $years = '';
        for ($i = $currentYear; $i > 1920; $i--) {
            $years .= '<option value=' . $i . '>' . $i . '</option>' . "\n";
        }
        return $years;
    }


}