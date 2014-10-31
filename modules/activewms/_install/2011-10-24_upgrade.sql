--
-- Table structure for table `styles`
--
ALTER TABLE `styles`
 ADD COLUMN `default_categoryid` varchar(64) NOT NULL DEFAULT '' AFTER `supplierid`;
ALTER TABLE `styles`
 ADD COLUMN `default_sportid` varchar(64) NOT NULL DEFAULT '' AFTER `supplierid`;

--
-- Table structure for table `styles_images`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `styles_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `styleid` varchar(64) NOT NULL DEFAULT '',
  `colourid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `image` longblob DEFAULT NULL,
  `image_name` varchar(128) NOT NULL default '',
  `image_type` varchar(100) default '',
  `alt_text` varchar(100) default '',
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
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `styleid` (`styleid`, `displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `parentid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `weburl` varchar(64) DEFAULT '',
  `meta_title` varchar(128) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `banner_image` longblob DEFAULT NULL,
  `banner_image_name` varchar(128) NOT NULL default '',
  `banner_image_type` varchar(100) default '',
  `displayfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `displayuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
CREATE TABLE `category_overrides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `categoryid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(128) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `weburl` varchar(64) DEFAULT '',
  `meta_title` varchar(128) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `banner_image` longblob DEFAULT NULL,
  `banner_image_name` varchar(128) NOT NULL default '',
  `banner_image_type` varchar(100) default '',
  `displayfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `displayuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `categorylang` (`categoryid`,`site`),
  KEY `site` (`site`),
  KEY `inactive` (`inactive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stylestocategories`
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylestocategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `styleid` varchar(64) NOT NULL,
  `categoryid` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `styleid` (`styleid`),
  KEY `categoryid` (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sports`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `parentid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `weburl` varchar(64) DEFAULT '',
  `meta_title` varchar(128) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `banner_image` longblob DEFAULT NULL,
  `banner_image_name` varchar(128) NOT NULL default '',
  `banner_image_type` varchar(100) default '',
  `displayfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `displayuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
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
-- Table structure for table `sport_overrides`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sport_overrides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `sportid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(128) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `webdescription` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `weburl` varchar(64) DEFAULT '',
  `meta_title` varchar(128) NOT NULL default '',
  `meta_description` varchar(255) NOT NULL default '',
  `meta_keywords` varchar(255) NOT NULL default '',
  `banner_image` longblob DEFAULT NULL,
  `banner_image_name` varchar(128) NOT NULL default '',
  `banner_image_type` varchar(100) default '',
  `displayfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `displayuntil` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `sport_site` (`sportid`,`site`),
  KEY `site` (`site`),
  KEY `inactive` (`inactive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stylestosports`
--
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylestosports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `styleid` varchar(64) NOT NULL,
  `sportid` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `styleid` (`styleid`),
  KEY `sportid` (`sportid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `apiaccessible`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','Categories','catg','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','categories','categories LEFT JOIN categories AS `parents` (ON categories.parentid = parents.uuid)','modules/activewms/categories_addedit.php',NULL,'modules/activewms/categories_addedit.php',NULL,NULL,'Admin',NULL,'Admin','Admin','deactivate',0,0,1,'categories.id !=0','categories.name',NULL,NULL,NULL,1, NOW(), 1, NOW());
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','name','if(categories.webdescription, concat(\'[b]\', categories.name,\'[/b][br]\',categories.webdescription), concat(\'[b]\', categories.name,\'[/b]\'))','left','',1,'categories.name',0,'100%','bbcode','');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','parent category','if(isnull(parents.uuid), \'No Parent\', parents.name)','left','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','display order','categories.displayorder','right','',4,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','web','categories.webenabled','center','',0,'',0,'','boolean','');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','All Records','categories.id != 0',1,'');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','Active Records','categories.inactive=0',1,'');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','Inactive Records','categories.inactive=1',2,'');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','select','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','edit','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','new','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','import','1',0,0,'',0);
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','categories.name','name',0,'field');
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:0257a694-fe26-11e0-be70-0017083b723b','categories.id','id',1,'field');


INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `apiaccessible`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','Sports','sprt','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','sports','sports LEFT JOIN sports AS `parents` ON (sports.parentid = parents.uuid)','modules/activewms/sports_addedit.php',NULL,'modules/activewms/sports_addedit.php',NULL,NULL,'Admin',NULL,'Admin','Admin','deactivate',0,0,1,'sports.id !=0','sports.name',NULL,NULL,NULL,1, NOW(), 1, NOW());
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','name','if(sports.webdescription, concat(\'[b]\', sports.name,\'[/b][br]\',sports.webdescription), concat(\'[b]\', sports.name,\'[/b]\'))','left','',1,'sports.name',0,'100%','bbcode','');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','parent category','if(isnull(parents.uuid), \'No Parent\', parents.name)','left','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','display order','sports.displayorder','right','',4,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','web','sports.webenabled','center','',0,'',0,'','boolean','');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','All Records','sports.id != 0',1,'');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','Active Records','sports.inactive=0',1,'');
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','Inactive Records','sports.inactive=1',2,'');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','select','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','edit','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','new','1',0,0,'',0);
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','import','1',0,0,'',0);
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','sports.name','name',0,'field');
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:3492da16-fe26-11e0-a58e-0017083b723b','sports.id','id',1,'field');

INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:d3e114ff-1e61-57aa-f0a3-5e485e2a4246','classification','styles entry','modules/activewms/styles_classification.php',11,0,NULL,NULL,NULL,1, NOW(), 1, NOW());

INSERT INTO `smartsearches` (`uuid`, `name`, `fromclause`, `valuefield`, `displayfield`, `secondaryfield`, `classfield`, `searchfields`, `filterclause`, `rolefield`, `tabledefid`, `moduleid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('smsr:07930178-0400-11e1-90f1-0017083b723b','Pick Sport For Style','sports','sports.uuid','sports.name','\'\'','\'\'','sports.name','sports.inactive = 0','\'\'','tbld:3492da16-fe26-11e0-a58e-0017083b723b','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42', 1, NOW(), 1, NOW());
INSERT INTO `smartsearches` (`uuid`, `name`, `fromclause`, `valuefield`, `displayfield`, `secondaryfield`, `classfield`, `searchfields`, `filterclause`, `rolefield`, `tabledefid`, `moduleid`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('smsr:3dc26248-0400-11e1-befc-0017083b723b','Pick Category For Style','categories','categories.uuid','categories.name','\'\'','\'\'','categories.name','categories.inactive = 0','\'\'','tbld:0257a694-fe26-11e0-be70-0017083b723b','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42', 1, NOW(), 1, NOW());

UPDATE `smartsearches` SET `name` = 'Pick Style Category For Style' WHERE `uuid` = 'smsr:1c1eb841-b799-6034-3ec2-127f04825851';

INSERT INTO `tabledefs` VALUES
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','Style Images','simg','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','styles_images','styles_images left join styles on styles_images.styleid=styles.uuid left join suppliers on styles.supplierid = suppliers.uuid','modules/activewms/styles_images_addedit.php','Admin','modules/activewms/styles_images_addedit.php','Admin',NULL,'Admin',NULL,NULL,'Admin','deactivate',0,0,1,'styles_images.id =-100','styles.stylenumber, styles_images.displayorder',NULL,NULL,NULL,1,NOW(),1,NOW());
INSERT INTO `tablecolumns` VALUES
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','style','styles.stylenumber','left','',0,'',0,'',NULL,''),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','style name','styles.stylename','left','',1,'',0,'',NULL,''),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','name','styles_images.name','left','',2,'',0,'',NULL,''),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','alt text','styles_images.alt_text','left','',3,'',0,'',NULL,''),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','display order','styles_images.displayorder','left','',4,'',0,'',NULL,'');
INSERT INTO `tablefindoptions` VALUES
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','All Records','styles_images.id!=-1',0,'');
INSERT INTO `tableoptions` VALUES
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','select','1',0,0,'Admin',0),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','edit','1',0,0,'Admin',0),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','new','1',0,0,'Admin',0),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','import','1',0,0,'Admin',0);
INSERT INTO `tablesearchablefields` VALUES
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','styles.stylenumber','style number',1,'field'),
(NULL,'tbld:76d711ca-0412-11e1-a021-0017083b723b','styles.stylename','name',2,'field');
