--
-- Table structure for table `product_feeds`
--

DROP TABLE IF EXISTS `product_feeds`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `site` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(64) DEFAULT '',
  `fileformat` varchar(64) DEFAULT '',
  `uploadurl` varchar(64) DEFAULT '',
  `username` varchar(64) DEFAULT '',
  `password` varchar(64) DEFAULT '',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

