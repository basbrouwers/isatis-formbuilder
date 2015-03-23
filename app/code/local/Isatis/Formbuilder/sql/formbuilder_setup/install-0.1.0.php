<?php

/**
 * @var $installer Isatis_Formbuilder_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

/*================================FORM================================================= */
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
    ->addColumn('receiver', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
        ''
    ), 'Action')
    ->addColumn('sendmethod', Varien_Db_Ddl_Table::TYPE_LONGVARCHAR, null, array(
        'nullable' => false,
    ), 'Sort order')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date');

$installer->getConnection()->createTable($formTable);



/*============================FIELDSET=========================================== */
$fieldsetTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/fieldset'))
    ->addColumn(
        'fieldset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
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
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Sort order')
    ->addColumn('pagenumber', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        ''
    ), 'Pagenumber')
    ->addColumn('column', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Columnr')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date')

    ->addIndex('INDEX_FORM','form_id');

$installer->getConnection()->createTable($fieldsetTable);


/*=================================ELEMENT==================================================== */
$elementTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/element'))
    ->addColumn(
        'element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true
    ), 'ID')
    ->addColumn(
        'fieldset_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Form ID')
    ->addColumn(
        'parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Parent ID')
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
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Sort order')
    ->addColumn('required', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Required')
    ->addColumn('validationrule', Varien_Db_Ddl_Table::TYPE_LONGVARCHAR, null, array(
        'nullable' => false,
    ), 'Validation rule')
    ->addColumn('placeholder', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Placeholder')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date')

    ->addIndex('INDEX_FIELDSET','fieldset_id');


$groupElementTable = $installer->getConnection()->newTable($installer->getTable('formbuilder/groupelement'))
    ->addColumn('groupelement_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'unsigned' => true,
    'nullable' => false,
    'primary' => true,
    'identity' => true
), 'ID')
    ->addColumn(
        'element_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Element ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false), 'Name')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Value')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true
    ), 'Label')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Sort order')
    ->addColumn('tstamp', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Last updated')
    ->addColumn('crdate', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable' => false,
    ), 'Creation date')
    ->addIndex('INDEX_GROUELEMENT','groupelement_id');

$installer->getConnection()->createTable($groupElementTable);

/*==================================OPTION=================================================== */
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
    ), 'Creation date')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable' => false,
    ), 'Sort order')
    ->addIndex('INDEX_ELEMENT','element_id');

$installer->getConnection()->createTable($optionTable);


/**
 * Add the forreign key constrains
 */
$installer->getConnection()
    ->addForeignKey('formFieldset',
                    $installer->getTable('formbuilder/fieldset'),
                    'form_id',
                    $installer->getTable('formbuilder/form'),
                    'form_id',
                    'cascade',
                    'cascade'
    );

$installer->getConnection()
    ->addForeignKey('fieldsetElement',
                    $installer->getTable('formbuilder/element'),
                    'fieldset_id',
                    $installer->getTable('formbuilder/fieldset'),
                    'fieldset_id',
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