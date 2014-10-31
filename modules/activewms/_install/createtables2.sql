DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `orderdate` datetime default NULL,
  `leadsource` varchar(64) default NULL,
  `weborder` tinyint(1) default '0',
  `webconfirmationno` varchar(64) default '',
  `clientid` VARCHAR(64),
  `billingaddressid` VARCHAR(64),
  `billtoemail` varchar(128) default NULL,
  `billtoname` varchar(128) default NULL,
  `billtoaddress1` varchar(128) default NULL,
  `billtoaddress2` varchar(128) default NULL,
  `billtocity` varchar(64) default NULL,
  `billtostate` varchar(5) default NULL,
  `billtopostcode` varchar(15) default NULL,
  `billtocountry` varchar(64) default '',
  `shiptosameasbilling` tinyint(3) unsigned NOT NULL default '0',
  `shiptoaddressid` VARCHAR(64),
  `shiptoname` varchar(128) default NULL,
  `shiptoaddress1` varchar(128) default NULL,
  `shiptoaddress2` varchar(128) default NULL,
  `shiptocity` varchar(64) default NULL,
  `shiptostate` varchar(20) default NULL,
  `shiptopostcode` varchar(15) default NULL,
  `shiptocountry` varchar(64) default NULL,
  `shiptotelephone` varchar(128) default NULL,
  `statusid` VARCHAR(64),
  `statusdate` datetime default NULL,
  `shippingmethod` varchar(128),
  `totalweight` double default '0',
  `trackingno` varchar(64) default NULL,
  `shipping` double default '0',
  `totalcost` double default '0',
  `totalti` double default '0',
  `printedinstructions` text,
  `specialinstructions` text,
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY `theid` (`id`),
  UNIQUE KEY (`uuid`),
  KEY `client` (`clientid`)
)  ENGINE=INNODB AUTO_INCREMENT=1000 PACK_KEYS=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE `orderitems` (
  `id` int(11) NOT NULL auto_increment,
  `uuid` varchar(64) NOT NULL,
  `orderid` int(11) NOT NULL default '0',
  `displayorder` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `productid` VARCHAR(64),
  `upc` varchar(128) DEFAULT NULL,
  `brand` varchar(128) DEFAULT NULL,
  `stylename` varchar(128) DEFAULT NULL,
  `size` varchar(128) DEFAULT NULL,
  `colour` varchar(128) DEFAULT NULL,
  `quantity` double default NULL,
  `unitcost` double default NULL,
  `unitpromocode` varchar(128) DEFAULT NULL,
  `unitdiscount` double default NULL,
  `unitprice` double default NULL,
  `unitweight` double default NULL,
  `memo` text,
  `taxable` tinyint(4) NOT NULL default '1',
  `createdby` int(11) NOT NULL default '0',
  `creationdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifiedby` int(11) default NULL,
  `modifieddate` timestamp(14) NOT NULL,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY (`id`),
  KEY `order` (`orderid`),
  KEY `product` (`productid`)
)  ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `orderstatuses`;
CREATE TABLE `orderstatuses` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` VARCHAR(128),
  `setreadytopost` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `orderdefault` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `defaultassignedtoid` VARCHAR(64),
  `inactive` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `priority` INTEGER UNSIGNED NOT NULL DEFAULT 0,
  `createdby` INTEGER UNSIGNED,
  `creationdate` DATETIME,
  `modifiedby` INTEGER UNSIGNED,
  `modifieddate` TIMESTAMP,
  `custom1` DOUBLE,
  `custom2` DOUBLE,
  `custom3` DATETIME,
  `custom4` DATETIME,
  `custom5` VARCHAR(255),
  `custom6` VARCHAR(255),
  `custom7` TINYINT(1),
  `custom8` TINYINT(1),
  PRIMARY KEY(`id`),
  UNIQUE KEY (`uuid`)
)  ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `orderstatushistory`;
CREATE TABLE `orderstatushistory` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderdefault` INTEGER UNSIGNED,
  `orderid` VARCHAR(64) NOT NULL,
  `orderstatusid` VARCHAR(64) NOT NULL,
  `statusdate` DATE,
  `assignedtoid` VARCHAR(64),
  PRIMARY KEY(`id`)
)  ENGINE=INNODB DEFAULT CHARSET=utf8;
