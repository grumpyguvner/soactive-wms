--
-- Table structure for table `brochure_requests`
--

DROP TABLE IF EXISTS `brochure_requests`;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brochure_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `site` varchar(128) NOT NULL,
  `reference` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `postal_code` varchar(128) NOT NULL,
  `country` varchar(128) DEFAULT 'UK',
  `carrier_sheet` varchar(128) DEFAULT NULL,
  `opted_out` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `undelivered` int(10) unsigned DEFAULT 0,
  `duplicate_id` int(11) NOT NULL DEFAULT '0',
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

