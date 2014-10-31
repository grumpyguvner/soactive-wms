--
-- Table structure for table `productsalesbylocation`
--
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productsalesbylocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `sale_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sale_id` varchar(64) NOT NULL DEFAULT '',
  `line` int(11) NOT NULL DEFAULT '0',
  `styleid` varchar(64) NOT NULL DEFAULT '',
  `productid` varchar(64) NOT NULL DEFAULT '',
  `plu` varchar(255) NOT NULL DEFAULT 'ERROR',
  `locationid` varchar(64) NOT NULL DEFAULT '',
  `quantity_sold` double(15,2) DEFAULT '0',
  `unitcost` double(15,2) DEFAULT NULL,
  `unitprice` double(15,2) DEFAULT NULL,
  `discount` double(15,2) DEFAULT NULL,
  `discount_type` char(1) DEFAULT NULL,
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uuid`),
  UNIQUE KEY (`sale_date`,`sale_id`,`line`,`plu`,`locationid`),
  KEY (`styleid`),
  KEY (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE productsalesbylocation ADD INDEX productsalesbylocation_location (locationid), ADD INDEX productsalesbylocation_product (productid), ADD INDEX productsalesbylocation_style (styleid);

INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `apiaccessible`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:537a027a-edc7-11e0-8bec-001d0923519e','Product Sales by Location','psal','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','productsalesbylocation','productsalesbylocation left join products on productsalesbylocation.productid=products.uuid left join styles on product.styleid=styles.uuid','modules/activewms/productsales.php',NULL,'modules/activewms/productsales.php',NULL,NULL,'Admin',NULL,'Admin','Admin','deactivate',0,0,1,'products.id =-100','styles.stylenumber',NULL,NULL,NULL,1, NOW(), 1, NOW());

--
-- Table structure for table `productsalesbylocation`
--
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productreceiptsbylocation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `receipt_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `receipt_id` varchar(64) NOT NULL DEFAULT '',
  `line` int(11) NOT NULL DEFAULT '0',
  `styleid` varchar(64) NOT NULL DEFAULT '',
  `productid` varchar(64) NOT NULL DEFAULT '',
  `plu` varchar(255) NOT NULL DEFAULT 'ERROR',
  `locationid` varchar(64) NOT NULL DEFAULT '',
  `quantity_received` double(15,2) DEFAULT '0',
  `unitcost` double(15,2) DEFAULT NULL,
  `unitprice` double(15,2) DEFAULT NULL,
  `discount` double(15,2) DEFAULT NULL,
  `discount_type` char(1) DEFAULT NULL,
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uuid`),
  UNIQUE KEY (`receipt_date`,`receipt_id`,`line`,`plu`,`locationid`),
  KEY (`styleid`),
  KEY (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE productreceiptsbylocation ADD INDEX productreceiptsbylocation_location (locationid), ADD INDEX productreceiptsbylocation_product (productid), ADD INDEX productreceiptsbylocation_style (styleid);

INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `apiaccessible`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:cd4b3178-f876-11e0-848d-0017083b723b','Product Receipts by Location','prec','table','mod:6ed4e9fa-76f8-11df-a1a0-00238b586e42','productreceiptsbylocation','productreceiptsbylocation left join products on productreceiptsbylocation.productid=products.uuid left join styles on product.styleid=styles.uuid','modules/activewms/productreceipts.php',NULL,'modules/activewms/productreceipts.php',NULL,NULL,'Admin',NULL,'Admin','Admin','deactivate',0,0,1,'products.id =-100','styles.stylenumber',NULL,NULL,NULL,1, NOW(), 1, NOW());
