<?php

/**
 * @var $installer Isatis_Formbuilder_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$formTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/form'))
    ->addColumn(
        'form_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true
    ), 'ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ''
    ), 'Title')
    ->addColumn('template', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false
    ), 'Page Template')
    ->addColumn('subtemplate', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false
    ), 'Form Template')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date');

$installer->getConnection()->createTable($formTable);

$formElementTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/formelement'))
    ->addColumn(
        'formelement_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true
    ), 'ID')
    ->addColumn(
        'form_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Form ID')
    ->addColumn('legend', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ''
    ), 'Legend')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date');

$installer->getConnection()->createTable($formElementTable);
$installer->getConnection()->addKey($installer->getTable('formbuilder/formelement'),'INDEX_FORM','form_id');


$elementTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/element'))
    ->addColumn(
        'element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true
    ), 'ID')
    ->addColumn(
        'formelement_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Form ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false), 'Name')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Value')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Label'
    )
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Type'
    )
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date');


$installer->getConnection()->createTable($elementTable);
$installer->getConnection()->addKey($installer->getTable('formbuilder/element'),'INDEX_FORMELEMENT','formelement_id');

$optionTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/option'))
    ->addColumn(
        'option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true
    ), 'ID')
    ->addColumn(
        'element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Form ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false
    ), 'Value')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Label')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date');

$installer->getConnection()->createTable($optionTable);
$installer->getConnection()->addKey($installer->getTable('formbuilder/option'),'INDEX_ELEMENT','element_id');

/**
 * Add the forreign key constrains
 */
$installer->getConnection()
    ->addForeignKey('formElementForm',
                    $installer->getTable('formbuilder/formelement'),
                    'form_id',
                    $installer->getTable('formbuilder/form'),
                    'form_id',
                    'cascade',
                    'cascade'
    );


$installer->getConnection()
    ->addForeignKey('formElement',
                    $installer->getTable('formbuilder/element'),
                    'formelement_id',
                    $installer->getTable('formbuilder/formelement'),
                    'formelement_id',
                    'cascade',
                    'cascade'
    );

$installer->getConnection()
    ->addForeignKey('elementOption',
                    $installer->getTable('formbuilder/option'),
                    'element_id',
                    $installer->getTable('formbuilder/element'),
                    'element_id',
                    'cascade',
                    'cascade'
    );


$installer->endSetup();