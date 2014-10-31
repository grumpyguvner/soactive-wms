--
-- Table structure for table `categories`
--
ALTER TABLE `categories`
 ADD COLUMN `google_taxonomy` varchar(128) NOT NULL DEFAULT '' AFTER `meta_keywords`;

--
-- Table structure for table `styles`
--
ALTER TABLE `styles`
 ADD COLUMN `agegroupid` varchar(64) NOT NULL DEFAULT '' AFTER `sizeguideid`,
 ADD COLUMN `genderid` varchar(64) NOT NULL DEFAULT '' AFTER `sizeguideid`;

--
-- Table structure for table `genders`
--
CREATE TABLE `genders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `google_taxonomy` varchar(64) NOT NULL DEFAULT '',
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

INSERT INTO `genders` (`uuid`, `name`, `inactive`, `priority`, `google_taxonomy`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`)
        VALUES (CONCAT('gend:',UUID()), 'Female', 0, 0, 'Female', 1, NOW(), 2, NOW()),
               (CONCAT('gend:',UUID()), 'Male', 0, 1, 'Male', 1, NOW(), 2, NOW()),
               (CONCAT('gend:',UUID()), 'Unisex', 0, 2, 'Unisex', 1, NOW(), 2, NOW());
--
-- Table structure for table `genders_translations`
--
CREATE TABLE `gender_overrides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `genderid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `google_taxonomy` varchar(64) NOT NULL DEFAULT '',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `gender_site` (`genderid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `agegroups`
--
CREATE TABLE `agegroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `google_taxonomy` varchar(64) NOT NULL DEFAULT '',
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

INSERT INTO `agegroups` (`uuid`, `name`, `inactive`, `priority`, `google_taxonomy`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`)
        VALUES (CONCAT('ageg:',UUID()), 'Adult', 0, 0, 'Adult', 1, NOW(), 2, NOW()),
               (CONCAT('ageg:',UUID()), 'Kids', 0, 1, 'Kids', 1, NOW(), 2, NOW());

--
-- Table structure for table `agegroup_translations`
--
CREATE TABLE `agegroup_overrides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL DEFAULT '',
  `agegroupid` varchar(64) NOT NULL DEFAULT '',
  `site` varchar(10) NOT NULL DEFAULT '',
  `name` varchar(128) DEFAULT NULL,
  `google_taxonomy` varchar(64) NOT NULL DEFAULT '',
  `inactive` tinyint(4) NOT NULL DEFAULT '0',
  `createdby` int(11) NOT NULL DEFAULT '0',
  `creationdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifiedby` int(11) DEFAULT NULL,
  `modifieddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `agegroup_site` (`agegroupid`,`site`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
