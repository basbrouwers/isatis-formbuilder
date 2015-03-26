/*
SQLyog Enterprise v12.08 (64 bit)
MySQL - 5.6.17 : Database - magento
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`magento` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `magento`;

/*Table structure for table `isa_formbuilder_element` */

DROP TABLE IF EXISTS `isa_formbuilder_element`;

CREATE TABLE `isa_formbuilder_element` (
  `element_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `fieldset_id` int(10) unsigned NOT NULL COMMENT 'Form ID',
  `parent_id` int(10) DEFAULT NULL COMMENT 'parent element id',
  `name` text NOT NULL COMMENT 'Name',
  `value` text COMMENT 'Value',
  `label` text COMMENT 'Label',
  `type` text COMMENT 'Type',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last updated',
  `crdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date',
  `sort_order` int(11) NOT NULL COMMENT 'Sort order',
  `required` smallint(1) DEFAULT '0' COMMENT 'Is field required',
  `validationrule` text COMMENT 'rule to validate the field',
  `placeholder` text COMMENT 'placeholder value for input',
  `parentdependency` tinyint(1) DEFAULT '0' COMMENT 'Is element depend on parent element',
  PRIMARY KEY (`element_id`),
  KEY `INDEX_FIELDSET` (`fieldset_id`),
  CONSTRAINT `fieldsetElement` FOREIGN KEY (`fieldset_id`) REFERENCES `isa_formbuilder_fieldset` (`fieldset_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=436 DEFAULT CHARSET=utf8 COMMENT='isa_formbuilder_element';

/*Data for the table `isa_formbuilder_element` */

insert  into `isa_formbuilder_element`(`element_id`,`fieldset_id`,`parent_id`,`name`,`value`,`label`,`type`,`tstamp`,`crdate`,`sort_order`,`required`,`validationrule`,`placeholder`,`parentdependency`) values (100,87,87,'geboortedatum',NULL,'Geboortedatum','date','2015-02-24 08:38:11','2015-02-24 08:38:11',7,0,NULL,NULL,0),(140,87,87,'geslacht','mevr','Mevr.','radio','2015-03-10 08:19:18','0000-00-00 00:00:00',6,0,NULL,NULL,0),(144,87,87,'voornaam',NULL,'Voornaam','text','2015-03-10 08:20:43','2015-03-10 08:20:43',0,0,NULL,NULL,0),(145,88,88,'straat',NULL,'Straat','text','2015-03-10 08:36:28','2015-03-10 08:36:28',1,0,NULL,NULL,0),(147,88,88,'plaats',NULL,'Plaats','text','2015-03-10 08:37:14','2015-03-10 08:37:14',3,0,NULL,NULL,0),(148,88,88,'telefoon',NULL,'Telefoonnummer','text','2015-03-10 08:37:35','2015-03-10 08:37:35',4,0,NULL,NULL,0),(150,88,88,'huisnummer',NULL,'Huisnr+toevoeging','text','2015-03-10 08:39:20','0000-00-00 00:00:00',2,0,NULL,NULL,0),(155,88,88,'email',NULL,'Emailadres','text','2015-03-10 08:46:36','0000-00-00 00:00:00',5,0,NULL,NULL,0),(161,87,87,'bsnnummer',NULL,'BSN Nummer','text','2015-03-10 10:43:00','0000-00-00 00:00:00',8,0,NULL,NULL,0),(162,87,87,'beroep',NULL,'Beroep','text','2015-03-10 10:43:09','2015-03-10 10:43:09',9,0,NULL,NULL,0),(163,87,87,'huisarts',NULL,'Huisarts','text','2015-03-10 10:43:21','2015-03-10 10:43:21',10,0,NULL,NULL,0),(164,87,87,'geboorteland',NULL,'Geboorteland','text','2015-03-10 10:43:36','2015-03-10 10:43:36',11,0,NULL,NULL,0),(165,87,87,'inndederland',NULL,'In Nederland sinds','text','2015-03-19 09:21:36','2015-03-10 10:43:58',12,NULL,'validate-no-html-tags','1975',0),(168,88,88,'kinderen',NULL,'Kind(eren)','label','2015-03-10 10:59:13','2015-03-10 10:59:13',6,0,NULL,NULL,0),(169,88,88,'gewicht',NULL,'Gewicht kinderen als deze jonger dan 15 jaar zijn','text','2015-03-10 10:59:58','2015-03-10 10:59:58',7,0,NULL,NULL,0),(170,88,88,'',NULL,'Gaat/gaan  uw kind(eren) naar de creche?','label','2015-03-13 14:14:54','2015-03-10 11:01:35',8,NULL,'validate-no-html-tags',NULL,0),(173,88,88,'creche','ja','ja','radio','2015-03-10 11:04:36','0000-00-00 00:00:00',9,0,NULL,NULL,0),(174,88,88,'creche','nee','Nee','radio','2015-03-10 11:04:43','0000-00-00 00:00:00',10,0,NULL,NULL,0),(175,91,91,'vertrekdatum',NULL,'Vertrekdatum','date','2015-03-10 11:29:26','2015-03-10 11:29:26',0,0,NULL,NULL,0),(176,91,91,'',NULL,'(Graag onderstaande tabel zo gedetaillerrd mogelijk invullen)','label','2015-03-10 11:32:12','2015-03-10 11:32:12',1,0,NULL,NULL,0),(187,87,87,'achternaam',NULL,'Achternaam','text','2015-03-17 14:18:58','0000-00-00 00:00:00',3,NULL,'validate-no-html-tags','Achternaam',0),(208,87,87,'aanhef',NULL,'Aanhef','label','2015-03-11 19:55:27','0000-00-00 00:00:00',4,NULL,'validate-no-html-tags',NULL,0),(209,87,87,'initialen',NULL,'Initialen','text','2015-03-12 08:54:57','0000-00-00 00:00:00',2,NULL,'validate-no-html-tags','Bas',0),(210,88,88,'','Dit is de disclaimer van het formuluier! \r\nraesent dapibus elementum lacus. Duis ultrices eget tortor eget consequat. Sed pretium turpis ornare felis tincidunt rhoncus. Sed vel suscipit purus. Curabitur dolor odio, pellentesque pulvinar elit id, consequat ullamcorper enim. Proin ultrices est vitae auctor imperdiet. Nulla eu massa ligula. Nunc egestas gravida massa, posuere sodales odio pellentesque vitae. ','Disclaimer','infobox','2015-03-19 10:45:40','2015-03-12 13:27:32',11,NULL,'validate-no-html-tags',NULL,0),(214,88,88,'asdasd',NULL,'Plaats','text','2015-03-24 12:02:21','2015-03-12 15:30:27',0,NULL,NULL,'asd',0),(218,87,87,'geboortedatum',NULL,'Geboortedatum','date','2015-03-17 14:19:41','2015-03-17 11:14:22',13,NULL,'validate-no-html-tags',NULL,0),(238,87,87,'geslacht','dhr','Dhr','radio','2015-03-19 09:01:49','0000-00-00 00:00:00',5,NULL,'validate-no-html-tags',NULL,0),(239,87,87,'rijbewijs',NULL,'Heeft u een rijbewijs?','yes-no','2015-03-19 10:52:52','0000-00-00 00:00:00',14,NULL,'validate-no-html-tags',NULL,0),(242,93,NULL,'voornaam',NULL,'Voornaam','text','2015-03-19 12:50:11','0000-00-00 00:00:00',0,NULL,'validate-cc-exp','voornaam',0),(243,93,NULL,'asdfasdf',NULL,NULL,'text','2015-03-19 12:50:22','0000-00-00 00:00:00',1,1,'validate-no-html-tags',NULL,0),(244,93,NULL,'disclaimer','\r\n                    Donec sem est, malesuada vitae lacus in, tincidunt varius ligula. Aenean eu purus ac dolor porttitor hendrerit. Quisque a tincidunt libero. Donec laoreet neque volutpat, blandit urna id, posuere leo.\r\n                ','Disclaimer','infobox','2015-03-19 12:51:38','0000-00-00 00:00:00',2,1,'validate-no-html-tags',NULL,0),(247,88,88,'asdasd',NULL,'Input field','text','2015-03-19 15:46:10','0000-00-00 00:00:00',12,NULL,'validate-no-html-tags','asdasd',0),(343,87,144,'gjh',NULL,'Input field','text','2015-03-24 12:00:54','2015-03-24 12:00:54',0,NULL,NULL,'cgh',0),(389,189,189,'',NULL,NULL,'select','2015-03-24 13:20:47','2015-03-24 13:20:47',0,NULL,NULL,NULL,0),(391,190,190,'dfgfg','fgfg',NULL,'select','2015-03-24 13:22:20','2015-03-24 13:22:20',0,NULL,NULL,NULL,0),(393,191,191,'xvcbxcvb',NULL,NULL,'select','2015-03-24 13:24:58','2015-03-24 13:24:58',0,NULL,NULL,NULL,0),(395,192,192,'',NULL,NULL,'select','2015-03-24 13:30:02','2015-03-24 13:29:46',0,NULL,NULL,NULL,0),(397,193,193,'asdfasdfasdf',NULL,NULL,'select','2015-03-24 13:44:30','2015-03-24 13:44:30',0,NULL,NULL,NULL,0),(400,195,195,'',NULL,'asdfasdfasdf','select','2015-03-24 14:15:55','2015-03-24 13:47:13',0,NULL,NULL,NULL,0),(401,195,195,'Main Input',NULL,'Main input','text','2015-03-24 14:35:34','2015-03-24 14:35:34',1,NULL,NULL,'asdfasdf',0),(402,195,401,'subinput',NULL,'SubInput','text','2015-03-24 14:35:48','2015-03-24 14:35:48',0,NULL,NULL,'dsfa',0),(403,195,195,'test','test','Heeft u nog opmerkingen','radio','2015-03-24 14:50:05','2015-03-24 14:50:05',2,NULL,NULL,NULL,0),(404,195,403,'xZXcZXc',NULL,'Dependent field','text','2015-03-24 15:08:17','2015-03-24 14:58:19',0,NULL,NULL,'duh',1),(405,196,196,'sdfsdfsdfdfsdf',NULL,'jhgjhgjhg','group','2015-03-26 08:54:00','2015-03-25 09:59:26',0,NULL,NULL,NULL,0),(406,196,405,'asdasd','asdsdasd','Radiobutton','radio','2015-03-25 09:59:50','2015-03-25 09:59:50',0,NULL,NULL,NULL,0),(407,196,405,'asdasdasd','asdasd','Radiobutton','radio','2015-03-25 09:59:59','2015-03-25 09:59:59',1,NULL,NULL,NULL,0),(408,196,405,'adfasdf','asdfasdf','Radiobutton','radio','2015-03-25 10:00:35','2015-03-25 10:00:35',0,NULL,NULL,NULL,0),(409,196,405,'asdfasdf','asdfasdf','Radiobutton','radio','2015-03-25 10:00:42','2015-03-25 10:00:42',0,NULL,NULL,NULL,0),(410,196,405,'fasdf','asdfasdf','Radiobutton','radio','2015-03-25 10:01:00','2015-03-25 10:01:00',0,NULL,NULL,NULL,0),(411,196,405,'m,bnbmn,','bnm,bmn,','Radiobutton','radio','2015-03-25 10:01:07','2015-03-25 10:01:07',0,NULL,NULL,NULL,0),(412,196,196,'parent activator',NULL,'Labeltje','checkbox','2015-03-25 10:28:27','2015-03-25 10:28:27',1,NULL,NULL,NULL,0),(413,196,412,'dependency input',NULL,'dependent','text','2015-03-25 10:29:18','2015-03-25 10:28:57',0,NULL,NULL,NULL,1),(414,196,196,'SDFASDFASDF',NULL,'zxcv','checkbox','2015-03-25 11:35:06','2015-03-25 11:35:06',2,NULL,NULL,NULL,0),(415,196,414,'DFDFDF','test','adsfasdf','checkbox','2015-03-25 11:53:35','2015-03-25 11:35:18',0,NULL,NULL,NULL,0),(416,196,196,'asdfasdfasdfasdf','dfasd','Input field','text','2015-03-26 08:37:01','2015-03-26 08:37:01',3,NULL,NULL,'asdfa',0),(417,197,197,'testlabel',NULL,'Dit is een enkel label','label','2015-03-26 09:34:20','2015-03-26 09:04:50',0,NULL,NULL,NULL,0),(418,197,197,'input type text',NULL,'E-mailadres','text','2015-03-26 09:38:04','2015-03-26 09:38:04',1,NULL,'validate-email','email',0),(419,197,197,'textarea',NULL,'Textarea','textarea','2015-03-26 09:38:41','2015-03-26 09:38:41',2,NULL,'validate-no-html-tags',NULL,0),(426,197,197,'selectbox',NULL,'Dropdown','select','2015-03-26 09:44:57','2015-03-26 09:44:57',3,NULL,NULL,NULL,0),(427,197,197,'single radio button','single radiobutton','Radiobutton','radio','2015-03-26 09:45:50','2015-03-26 09:45:50',4,NULL,NULL,NULL,0),(428,197,197,'single chcekbox','on','Checkbox','checkbox','2015-03-26 09:46:25','2015-03-26 09:46:11',5,1,NULL,NULL,0),(429,197,197,'html','<strong>TESTING CUSTOM HTML!</strong>','Custom HTML','customhtml','2015-03-26 09:49:51','2015-03-26 09:46:57',6,NULL,NULL,NULL,0),(430,197,197,'infobox','Donec sem est, malesuada vitae lacus in, tincidunt varius ligula. Aenean eu purus ac dolor porttitor hendrerit. Quisque a tincidunt libero. Donec laoreet neque volutpat, blandit urna id, posuere leo.\r\n                ','InfoBox','infobox','2015-03-26 10:55:34','2015-03-26 10:55:34',7,NULL,NULL,NULL,0),(431,197,197,'question parent dependency','1','Heeft u een rijbewijs?','checkbox','2015-03-26 10:59:26','2015-03-26 10:59:26',8,NULL,NULL,NULL,0),(432,197,431,'group',NULL,'Rijbewijzen','group','2015-03-26 11:13:21','2015-03-26 10:59:50',0,NULL,NULL,NULL,1),(433,197,432,'rijbewijsA','A','Rijbewijs A','checkbox','2015-03-26 11:18:13','2015-03-26 11:18:13',0,NULL,NULL,NULL,0),(434,197,432,'rijbewijsB','B','Rijbewijs B','checkbox','2015-03-26 11:18:29','2015-03-26 11:18:29',0,NULL,NULL,NULL,0),(435,197,432,'rijbewijsC','C','Rijbewijs C','checkbox','2015-03-26 11:19:11','2015-03-26 11:19:11',0,NULL,NULL,NULL,0);

/*Table structure for table `isa_formbuilder_fieldset` */

DROP TABLE IF EXISTS `isa_formbuilder_fieldset`;

CREATE TABLE `isa_formbuilder_fieldset` (
  `fieldset_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `form_id` int(10) unsigned NOT NULL COMMENT 'Form ID',
  `legend` text NOT NULL COMMENT 'Legend',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last updated',
  `crdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date',
  `sort_order` int(11) NOT NULL COMMENT 'Sort order',
  `pagenumber` int(11) NOT NULL DEFAULT '1' COMMENT 'Pagenumber',
  `column` int(11) DEFAULT '1' COMMENT 'Column containing the fieldset',
  PRIMARY KEY (`fieldset_id`),
  KEY `INDEX_FORM` (`form_id`),
  CONSTRAINT `formFieldset` FOREIGN KEY (`form_id`) REFERENCES `isa_formbuilder_form` (`form_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COMMENT='isa_formbuilder_fieldset';

/*Data for the table `isa_formbuilder_fieldset` */

insert  into `isa_formbuilder_fieldset`(`fieldset_id`,`form_id`,`legend`,`tstamp`,`crdate`,`sort_order`,`pagenumber`,`column`) values (87,114,'Mijn gegevens','2015-03-10 08:18:31','0000-00-00 00:00:00',0,1,1),(88,114,'Adresgegevens','2015-03-10 08:35:36','0000-00-00 00:00:00',1,1,2),(91,114,'Reisgegevens','2015-03-10 11:09:05','0000-00-00 00:00:00',2,1,1),(93,116,'','2015-03-19 12:48:25','2015-03-19 12:48:25',0,1,NULL),(187,244,'Fieldset','2015-03-24 13:00:11','2015-03-24 13:00:11',0,1,1),(188,245,'rereradf','0000-00-00 00:00:00','0000-00-00 00:00:00',0,1,1),(189,245,'Fieldsetssss','2015-03-24 13:01:29','2015-03-24 13:01:29',0,1,1),(190,246,'Fieldset','2015-03-24 13:22:10','2015-03-24 13:22:10',0,1,1),(191,247,'Fieldset','2015-03-24 13:24:41','2015-03-24 13:24:41',0,1,1),(192,248,'Fieldset','2015-03-24 13:29:15','2015-03-24 13:29:15',0,1,1),(193,249,'Fieldset','2015-03-24 13:44:12','2015-03-24 13:44:12',0,1,1),(194,250,'Legend','2015-03-24 13:45:08','2015-03-24 13:45:08',0,1,1),(195,251,'The Legend!','2015-03-24 13:46:33','0000-00-00 00:00:00',0,1,1),(196,252,'Adresgegevens','2015-03-25 09:59:09','0000-00-00 00:00:00',0,1,1),(197,253,'Alle elementen','2015-03-26 09:04:31','0000-00-00 00:00:00',0,1,1);

/*Table structure for table `isa_formbuilder_form` */

DROP TABLE IF EXISTS `isa_formbuilder_form`;

CREATE TABLE `isa_formbuilder_form` (
  `form_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` text NOT NULL COMMENT 'Title',
  `template` int(10) unsigned NOT NULL COMMENT 'Page Template',
  `subtemplate` int(10) unsigned NOT NULL COMMENT 'Form Template',
  `receiver` text NOT NULL COMMENT 'Action',
  `sendmethod` varchar(128) NOT NULL COMMENT 'Sort order',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last updated',
  `crdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date',
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=254 DEFAULT CHARSET=utf8 COMMENT='isa_formbuilder_form';

/*Data for the table `isa_formbuilder_form` */

insert  into `isa_formbuilder_form`(`form_id`,`title`,`template`,`subtemplate`,`receiver`,`sendmethod`,`tstamp`,`crdate`) values (114,'Reisadviesformulier',1,2,'bas@isatis.nl','0','0000-00-00 00:00:00','0000-00-00 00:00:00'),(116,'demoformulier',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(244,'asdasdasd',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(245,'integrety test',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(246,'xfgb',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(247,'xcvbxcvb',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(248,'sd',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(249,'asd',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(250,'sdfgsdfg',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(251,'dfasdfasdf',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(252,'test',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00'),(253,'testing all elements',0,1,'','','0000-00-00 00:00:00','0000-00-00 00:00:00');

/*Table structure for table `isa_formbuilder_group_element` */

DROP TABLE IF EXISTS `isa_formbuilder_group_element`;

CREATE TABLE `isa_formbuilder_group_element` (
  `groupelement_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `element_id` int(10) unsigned NOT NULL,
  `label` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `value` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT NULL,
  `type` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `tstamp` timestamp NULL DEFAULT NULL,
  `crdate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`groupelement_id`),
  KEY `elementgroup` (`element_id`),
  CONSTRAINT `elementgroup` FOREIGN KEY (`element_id`) REFERENCES `isa_formbuilder_element` (`element_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `isa_formbuilder_group_element` */

/*Table structure for table `isa_formbuilder_select_option` */

DROP TABLE IF EXISTS `isa_formbuilder_select_option`;

CREATE TABLE `isa_formbuilder_select_option` (
  `option_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `element_id` int(10) unsigned NOT NULL COMMENT 'Form ID',
  `value` text NOT NULL COMMENT 'Value',
  `label` text COMMENT 'Label',
  `tstamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Last updated',
  `crdate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Creation date',
  `sort_order` int(11) NOT NULL COMMENT 'Sort order',
  PRIMARY KEY (`option_id`),
  KEY `INDEX_ELEMENT` (`element_id`),
  CONSTRAINT `elementOption` FOREIGN KEY (`element_id`) REFERENCES `isa_formbuilder_element` (`element_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COMMENT='isa_formbuilder_select_option';

/*Data for the table `isa_formbuilder_select_option` */

insert  into `isa_formbuilder_select_option`(`option_id`,`element_id`,`value`,`label`,`tstamp`,`crdate`,`sort_order`) values (12,100,'2',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(13,100,'3',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(14,100,'4',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(15,100,'5',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(16,100,'6',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(17,100,'7',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(18,100,'8',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(19,100,'9',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(20,100,'10',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(21,100,'11',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(22,100,'12',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(23,100,'13',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(24,100,'14',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(25,100,'15',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(26,100,'16',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(27,100,'17',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(28,100,'18',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(29,100,'19',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(30,100,'20',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(31,100,'21',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(32,100,'22',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(33,100,'23',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(34,100,'24',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(35,100,'25',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(36,100,'26',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(37,100,'27',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(38,100,'28',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(39,100,'29',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(40,100,'30',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(41,100,'31',NULL,'2015-02-24 08:38:11','0000-00-00 00:00:00',0),(43,391,'dfgdfg',NULL,'2015-03-24 13:22:20','0000-00-00 00:00:00',0),(44,393,'xcvbcvb',NULL,'2015-03-24 13:24:58','0000-00-00 00:00:00',0),(45,393,'xcvbxcvb',NULL,'2015-03-24 13:24:58','0000-00-00 00:00:00',0),(46,393,'xcvbxcvb',NULL,'2015-03-24 13:24:58','0000-00-00 00:00:00',0),(47,395,'asdasd',NULL,'2015-03-24 13:29:46','0000-00-00 00:00:00',0),(48,395,'asdqwe',NULL,'2015-03-24 13:29:46','0000-00-00 00:00:00',0),(49,395,'asdasd',NULL,'2015-03-24 13:30:02','0000-00-00 00:00:00',0),(50,395,'asdasdasd',NULL,'2015-03-24 13:30:02','0000-00-00 00:00:00',0),(51,395,'zcvzxcvzxcvzxcv',NULL,'2015-03-24 13:30:02','0000-00-00 00:00:00',0),(52,395,'',NULL,'2015-03-24 13:30:02','0000-00-00 00:00:00',0),(53,397,'adsfasdfa eerste optiomn',NULL,'2015-03-24 13:44:30','0000-00-00 00:00:00',0),(54,400,'1','eerste option','2015-03-24 14:15:55','0000-00-00 00:00:00',0),(55,400,'23','tweede option','2015-03-24 14:15:55','0000-00-00 00:00:00',0),(56,400,'3','derde option','2015-03-24 14:15:55','0000-00-00 00:00:00',0),(60,426,'1','option1','2015-03-26 09:44:57','0000-00-00 00:00:00',0),(61,426,'2','option2','2015-03-26 09:44:57','0000-00-00 00:00:00',0),(62,426,'3','option3','2015-03-26 09:44:57','0000-00-00 00:00:00',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
