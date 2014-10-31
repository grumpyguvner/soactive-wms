--
-- Table structure for table `styles`
--
ALTER TABLE `styles`
 ADD COLUMN `sizeguideid` varchar(64) NOT NULL DEFAULT '' AFTER `supplierid`;

--
-- Table structure for table `sizeguides`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sizeguides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `parentid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `custom1` double DEFAULT NULL,
  `custom2` double DEFAULT NULL,
  `custom3` datetime DEFAULT NULL,
  `custom4` datetime DEFAULT NULL,
  `custom5` varchar(255) DEFAULT NULL,
  `custom6` varchar(255) DEFAULT NULL,
  `custom7` tinyint(1) DEFAULT NULL,
  `custom8` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=983 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `category_overrides`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sizeguide_overrides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `sizeguideid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(128) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `sizeguidesite` (`sizeguideid`,`site`),
  KEY `site` (`site`),
  KEY `inactive` (`inactive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `apiaccessible`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES
 ('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','Size Guides','szgd','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','sizeguides','sizeguides','modules/activewms/sizeguides_addedit.php',NULL,'modules/activewms/sizeguides_addedit.php',NULL,NULL,'Admin',NULL,'Admin','Admin','deactivate',0,0,1,'sizeguides.id !=0','sizeguides.name',NULL,NULL,NULL,1, NOW(), 1, NOW());
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','name','if(sizeguides.webdescription, concat(\'[b]\', sizeguides.name,\'[/b][br]\',sizeguides.webdescription), concat(\'[b]\', sizeguides.name,\'[/b]\'))','left','',1,'sizeguides.name',0,'100%','bbcode',''),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','display order','sizeguides.displayorder','right','',4,'',0,'',NULL,''),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','web','sizeguides.webenabled','center','',0,'',0,'','boolean','');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES 
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','All Records','sizeguides.id != 0',1,''),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','Active Records','sizeguides.inactive=0',1,''),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','Inactive Records','sizeguides.inactive=1',2,'');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES 
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','select','1',0,0,'',0),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','edit','1',0,0,'',0),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','new','1',0,0,'',0),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','import','1',0,0,'',0);
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES 
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','sizeguides.name','name',0,'field'),
('tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8','sizeguides.id','id',1,'field');
