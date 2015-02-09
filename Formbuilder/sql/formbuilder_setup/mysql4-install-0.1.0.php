<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('form')};
CREATE TABLE {$this->getTable('form')} (
  `form_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `template` int(11) NOT NULL default '',
  `subtemplate` int(11) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('element')};
CREATE TABLE {$this->getTable('element')} (
  `element_id` int(11) unsigned NOT NULL auto_increment,
  `form_id`, int(11) unsigned NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `value` varchar (128) NOT NULL default'',
  `label` varchar (128) NOT NULL default'',
  `crdate` int(11) unsigned NOT NULL,
  `tstamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`element_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();