
--
-- Table structure for table `colours`
--

DROP TABLE IF EXISTS `colours`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colours` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `bleepid` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `colours_translations`
--

DROP TABLE IF EXISTS `colours_translations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `colours_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `colourid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `colourlang` (`colourid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `groupid` varchar(64) NOT NULL,
  `categoryid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `bleepid` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `categoryid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `bleepid` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `bleepid` varchar(64) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `styleid` varchar(64) NOT NULL DEFAULT '',
  `colourid` varchar(64) NOT NULL DEFAULT '',
  `sizeid` varchar(64) NOT NULL DEFAULT '',
  `bleepid` varchar(11) NOT NULL DEFAULT '',
  `upc` varchar(128) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'In Stock',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `bleep_lastchanged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bleep_lastimport` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2084 DEFAULT CHARSET=utf8;

--
-- Table structure for table `producttypes`
--

DROP TABLE IF EXISTS `producttypes`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producttypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `producttypes_translations`
--

DROP TABLE IF EXISTS `producttypes_translations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `producttypes_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `producttypeid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `producttypelang` (`producttypeid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `productsbylocation`
--

DROP TABLE IF EXISTS `productsbylocation`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsbylocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `locationid` varchar(64) NOT NULL DEFAULT '',
  `productid` varchar(64) NOT NULL DEFAULT '',
  `quantity` double NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `prodandloc` (`locationid`,`productid`)
) ENGINE=InnoDB AUTO_INCREMENT=2084 DEFAULT CHARSET=utf8;

--
-- Table structure for table `sizes`
--

DROP TABLE IF EXISTS `sizes`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `bleepid` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `sizes_translations`
--

DROP TABLE IF EXISTS `sizes_translations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sizes_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `sizeid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `sizelang` (`sizeid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stylecategories`
--

DROP TABLE IF EXISTS `stylecategories`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylecategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `parentid` varchar(64) NOT NULL DEFAULT '',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `inactive` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `description` text,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `webdisplayname` varchar(64) DEFAULT '',
  `attractiveid` int(10) NOT NULL DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=639 DEFAULT CHARSET=utf8;

--
-- Table structure for table `stylecategories_translations`
--

DROP TABLE IF EXISTS `stylecategories_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylecategories_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `categoryid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(64) DEFAULT NULL,
  `description` text,
  `webdisplayname` varchar(64) DEFAULT '',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `categorylang` (`categoryid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `styles`
--

DROP TABLE IF EXISTS `styles`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `styles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `stylename` varchar(128) DEFAULT NULL,
  `stylenumber` varchar(32) NOT NULL DEFAULT '',
  `categoryid` varchar(64) NOT NULL DEFAULT '',
  `supplierid` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `isoversized` tinyint(4) NOT NULL DEFAULT '0',
  `isprepackaged` tinyint(4) NOT NULL DEFAULT '0',
  `packagesperitem` double DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'In Stock',
  `unitcost` double DEFAULT '0',
  `unitofmeasure` varchar(64) DEFAULT NULL,
  `unitprice` double DEFAULT '0',
  `saleprice` double DEFAULT '0',
  `salestartdate` datetime DEFAULT NULL,
  `saleenddate` datetime DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `webenabled` tinyint(1) NOT NULL DEFAULT '0',
  `keywords` varchar(128) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `webdescription` text,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `type` enum('Inventory','Non-Inventory','Service','Kit','Assembly') NOT NULL DEFAULT 'Inventory',
  `taxable` tinyint(4) NOT NULL DEFAULT '1',
  `memo` text,
  `bleep_alt_depts` varchar(64) NOT NULL DEFAULT '',
  `bleep_colours` varchar(128) NOT NULL DEFAULT '',
  `bleep_sizes` varchar(128) NOT NULL DEFAULT '',
  `bleep_lastchanged` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bleep_lastproduct` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bleep_lastimport` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  UNIQUE KEY `thstylenum` (`stylenumber`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2084 DEFAULT CHARSET=utf8;

--
-- Table structure for table `styles_translations`
--

DROP TABLE IF EXISTS `styles_translations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `styles_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `styleid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `stylename` varchar(128) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(128) DEFAULT NULL,
  `webdescription` text,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `stylelang` (`styleid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stylestocolours`
--

DROP TABLE IF EXISTS `stylestocolours`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylestocolours` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `styleid` varchar(64) NOT NULL,
  `colourid` varchar(64) NOT NULL,
  `bleepid` varchar(32) NOT NULL DEFAULT '',
  `thumbnail` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `styleid` (`styleid`),
  KEY `colourid` (`colourid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stylestosizes`
--

DROP TABLE IF EXISTS `stylestosizes`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylestosizes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `styleid` varchar(64) NOT NULL,
  `sizeid` varchar(64) NOT NULL,
  `bleepid` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `styleid` (`styleid`),
  KEY `sizeid` (`sizeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `stylestostylecategories`
--

DROP TABLE IF EXISTS `stylestostylecategories`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stylestostylecategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `styleid` varchar(64) NOT NULL,
  `stylecategoryid` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `styleid` (`styleid`),
  KEY `stylecategoryid` (`stylecategoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `bleepid` int(11) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(10) unsigned DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8;

--
-- Table structure for table `suppliers_translations`
--

DROP TABLE IF EXISTS `suppliers_translations`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `supplierid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `supplierlang` (`supplierid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET character_set_client = @saved_cs_client */;


