--tablecustomfields CREATE--
CREATE TABLE tablecustomfields (
  `id` int(11) NOT NULL auto_increment,
  `tabledefid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `field` varchar(8) NOT NULL default '',
  `format` varchar(32),
  `generator` TEXT,
  `required` TINYINT(4) NOT NULL default 0,
  `displayorder` int(11) NOT NULL default 0,
  `roleid` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `tabledef` (`tabledefid`)
) ENGINE=INNODB;
--end tablecustomfields CREATE--
--userpreferences CREATE--
CREATE TABLE `userpreferences` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` TEXT,
  PRIMARY KEY  (`id`),
  KEY `thename` (`name`)
) ENGINE=INNODB;
--end userpreferences CREATE--
--widgets CREATE--
CREATE TABLE `widgets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `file` varchar(255) NOT NULL,
  `roleid` VARCHAR(64),
  `moduleid` VARCHAR(64),
  `default` tinyint(4) NOT NULL default '0',
  `createdby` int(11) default NULL,
  `creationdate` datetime default NULL,
  `modifiedby` int(10) unsigned default NULL,
  `modifieddate` timestamp,
  PRIMARY KEY  (`id`),
  KEY `uniqueid` (`uuid`)
) ENGINE=INNODB;
--end widgets CREATE--
--reportsettings CREATE--
CREATE TABLE `reportsettings` (
  `id` int(11) NOT NULL auto_increment,
  `reportuuid` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL default '',
  `value` text default '',
  `type` varchar(32) NOT NULL default 'string',
  `required` tinyint(4) NOT NULL default '0',
  `defaultvalue` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB;
--end reportsettings CREATE--

--attachments ALTER--
ALTER TABLE `attachments` ENGINE=INNODB;
ALTER TABLE `attachments`
    MODIFY `fileid` VARCHAR(64) NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `recordid` VARCHAR(64);
--end attachemnts ALTER--
--choices ALTER--
ALTER TABLE `choices` ENGINE=INNODB;
--end choices ALTER--
--files ALTER--
ALTER TABLE `files` ENGINE=INNODB;
ALTER TABLE `files`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0,
    MODIFY `roleid` varchar(64);
--end files ALTER--
--log ALTER--
ALTER TABLE `log` ENGINE=INNODB;
ALTER TABLE `log`
    MODIFY `userid` VARCHAR(64);
--end log ALTER--
--menu ALTER--
ALTER TABLE `menu` ENGINE=INNODB;
ALTER TABLE `menu`
    ADD COlUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY COLUMN `parentid` varchar(64) DEFAULT '',
    MODIFY `roleid` varchar(64);
--end menu ALTER--
--modules ALTER--
ALTER TABLE `modules` ENGINE=INNODB;
ALTER TABLE `modules`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`;
--end modules ALTER--
UPDATE `modules` SET `uuid`='mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7' WHERE `id`='1';
--notes ALTER--
ALTER TABLE `notes` ENGINE=INNODB;
ALTER TABLE `notes`
    MODIFY `type` CHAR(2) NOT NULL DEFAULT 'NT',
    MODIFY `assignedtoid` varchar(64),
    MODIFY `attachedid` varchar(64),
    MODIFY `attachedtabledefid` varchar(64),
    MODIFY `parentid` varchar(64),
    MODIFY `assignedbyid` varchar(64),
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end notes ALTER--
--relationships ALTER--
ALTER TABLE `relationships` ENGINE=INNODB;
ALTER TABLE `relationships`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `fromtableid` VARCHAR(64) NOT NULL,
    MODIFY `totableid` VARCHAR(64) NOT NULL;
--end relationships ALTER--
--reports ALTER--
ALTER TABLE `reports` ENGINE=INNODB;
ALTER TABLE `reports`
    MODIFY `tabledefid` varchar(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64),
    MODIFY `reportfile` VARCHAR(128) NOT NULL,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`;
--end reports ALTER--
--roles ALTER--
ALTER TABLE `roles` ENGINE=INNODB;
ALTER TABLE `roles`
    MODIFY COLUMN `inactive` TINYINT(4) NOT NULL DEFAULT 0,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end roles ALTER--
--rolestousers ALTER--
ALTER TABLE `rolestousers` ENGINE=INNODB;
ALTER TABLE `rolestousers`
    MODIFY `userid` varchar(64),
    MODIFY `roleid` varchar(64);
--end rolestousers ALTER--
--scheduler ALTER--
ALTER TABLE `scheduler` ENGINE=INNODB;
ALTER TABLE `scheduler`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `pushrecordid` varchar(64) default '' AFTER `job`;
--end scheduler ALTER--
--settings ALTER--
ALTER TABLE `settings` ENGINE=INNODB;
--end settings ALTER--
--smartsearches ALTER--
ALTER TABLE `smartsearches` ENGINE=INNODB;
ALTER TABLE `smartsearches`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `moduleid` VARCHAR(64),
    MODIFY `tabledefid` VARCHAR(64);
--end smartsearches ALTER--
--tablecolumns ALTER--
ALTER TABLE `tablecolumns` ENGINE=INNODB;
ALTER TABLE `tablecolumns`
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablecolumns ALTER--
--tabledefs ALTER--
ALTER TABLE `tabledefs` ENGINE=INNODB;
ALTER TABLE `tabledefs`
    MODIFY COLUMN `defaultwhereclause` TEXT DEFAULT NULL,
    MODIFY COLUMN `defaultsortorder` TEXT,
    MODIFY `moduleid` VARCHAR(64) NOT NULL,
    MODIFY `editroleid` varchar(64),
    MODIFY `addroleid` varchar(64),
    MODIFY `searchroleid` varchar(64),
    MODIFY `advsearchroleid` varchar(64) default 'Admin',
    MODIFY `viewsqlroleid` varchar(64) default 'Admin',
    ADD COLUMN `importfile` VARCHAR(128) DEFAULT NULL AFTER `addroleid`,
    ADD COLUMN `importroleid` VARCHAR(64) NOT NULL DEFAULT 'Admin' AFTER `importfile`,
    ADD COLUMN `canpost` tinyint(4) NOT NULL default '0' AFTER `deletebutton`,
    ADD COLUMN `apiaccessible` tinyint(4) NOT NULL default '0' AFTER `deletebutton`,
    ADD COLUMN `hascustomfields` tinyint(4) NOT NULL default '0' AFTER `canpost`,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `prefix` VARCHAR(4) AFTER `displayname`;
--end tabledefs ALTER--
--tablefindoptions ALTER--
ALTER TABLE `tablefindoptions` ENGINE=INNODB;
ALTER TABLE `tablefindoptions`
    MODIFY COLUMN `search` TEXT NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablefindoptions ALTER--
--tablegroupings ALTER--
ALTER TABLE `tablegroupings` ENGINE=INNODB;
ALTER TABLE `tablegroupings`
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--end tablegroupings ALTER--
--tableoptions ALTER--
ALTER TABLE `tableoptions` ENGINE=INNODB;
ALTER TABLE `tableoptions`
    ADD COLUMN `needselect` BOOLEAN NOT NULL DEFAULT 1 AFTER `option`,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64) NOT NULL DEFAULT '';
--tableoptions ALTER--
--tablesearchablefields ALTER--
ALTER TABLE `tablesearchablefields` ENGINE=INNODB;
ALTER TABLE `tablesearchablefields`
    MODIFY COLUMN `field` TEXT NOT NULL,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL;
--end tablesearchablefields ALTER--
--tabs ALTER--
ALTER TABLE `tabs` ENGINE=INNODB;
ALTER TABLE `tabs`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `roleid` VARCHAR(64);
--end tabs ALTER--
--users ALTER--
ALTER TABLE `users` ENGINE=INNODB;
ALTER TABLE `users`
    ADD COLUMN `lastip` VARCHAR(45) NOT NULL DEFAULT '' AFTER `lastname`,
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    ADD COLUMN `custom1` DOUBLE,
    ADD COLUMN `custom2` DOUBLE,
    ADD COLUMN `custom3` DATETIME,
    ADD COLUMN `custom4` DATETIME,
    ADD COLUMN `custom5` VARCHAR(255),
    ADD COLUMN `custom6` VARCHAR(255),
    ADD COLUMN `custom7` TINYINT(1) DEFAULT 0,
    ADD COLUMN `custom8` TINYINT(1) DEFAULT 0;
--end users ALTER--
--usersearches ALTER--
ALTER TABLE `usersearches` ENGINE=INNODB;
ALTER TABLE `usersearches`
    ADD COLUMN `uuid` varchar(64) NOT NULL AFTER `id`,
    MODIFY `tabledefid` VARCHAR(64) NOT NULL,
    MODIFY `roleid` VARCHAR(64),
    MODIFY `userid` VARCHAR(64) NOT NULL;
--end usersearches ALTER--

--files UPDATE--
UPDATE `files` SET
    `uuid`='file:ad761197-e5a2-3fdf-f330-d1508f10813e',
    `roleid` = 'Admin'
WHERE
    `id`='1';
--end files UPDATE--
--menu INSERT--
DELETE FROM `menu`;
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', 'Tools', '', '', '3', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:d9e0eaa6-26b3-fcfb-f1b5-ee0eef8a857a', 'Notes', 'search.php?id=tbld%3Aa4cdd991-cf0a-916f-1240-49428ea1bdd1', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '30', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:03e984b6-d7ac-def2-a4f5-662003e94bfd', 'Tasks', 'search.php?id=tbld%3A2bc3e683-81f9-694a-9550-a0c7263057de', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '40', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:c4871074-90e9-c9bb-bcf9-b69ca0c30e8b', 'Events', 'search.php?id=tbld%3A0fcca651-6c34-c74d-ac04-2d88f602dd71', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '50', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2bcd88e6-703f-128c-7f18-1aad44fb46fb', 'Snapshot', 'modules/base/snapshot.php', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '10', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', 'System', '', '', '10', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e44cf976-658a-50d7-4a8f-b575713e3964', 'Configuration', 'modules/base/adminsettings.php', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '10', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:cf16add3-b02a-bd9b-b3c7-3fe9d0d2e0ba', 'Users', 'search.php?id=tbld%3Aafe6d297-b484-4f0b-57d4-1c39412e9dfb', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '40', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:d727dda4-6ac5-dd23-992b-7cf64cd96620', 'Roles', 'search.php?id=tbld%3A87b9fe06-afe5-d9c6-0fa0-4a0f2ec4ee8a', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '50', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:afddfee1-5ab7-2064-204f-816e9df929ac', '----', 'N/A', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '15', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:9c845b2d-7383-4182-1bf5-fe9b770f1d63', 'Menu', 'search.php?id=tbld%3A83187e3d-101e-a8a5-037f-31e9800fed2d', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '50', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:ef5853a0-3b57-06e5-a8d4-31bfbdb207b5', 'Files', 'search.php?id=tbld%3A80b4f38d-b957-bced-c0a0-ed08a0db6475', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '910', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:1f72cd68-1e5a-e718-3b38-8671da9b0a1d', 'Saved Searchs/Sorts', 'search.php?id=tbld%3Ae251524a-2da4-a0c9-8725-d3d0412d8f4a', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '930', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:00ddbccd-2761-3347-22ee-1adce9696b66', '----', 'N/A', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '45', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:8825339e-76a8-b51a-fdce-7b409451962c', 'Reports', 'search.php?id=tbld%3Ad595ef42-db9d-2233-1b9b-11dfd0db9cbb', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '70', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2dea83ff-2927-0859-ab97-530ee76e7bb8', 'Relationships', 'search.php?id=tbld%3A8d19c73c-42fb-d829-3681-d20b4dbe43b9', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '60', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:76f8b6cd-f42d-0823-3e12-5cbe39f7fbdb', 'Table Definitions', 'search.php?id=tbld%3A5c9d645f-26ab-5003-b98e-89e9049f8ac3', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '940', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f1780935-8018-d240-8e74-f8fde4f8e1bb', 'Modules', 'search.php?id=tbld%3Aea159d67-5e89-5b7f-f5a0-c740e147cd73', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '60', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e0a2cc66-9b44-f0cb-84a7-45eb3307298f', 'My Account', 'modules/base/myaccount.php', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:b63e3218-0a12-3e51-88b7-8af400a74a7e', 'Scheduler', 'search.php?id=tbld%3A83de284b-ef79-3567-145c-30ca38b40796', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '32', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:5f875b57-f499-2307-6d57-61ba49b72e82', 'System Log', 'search.php?id=tbld%3A3f71ab66-1f84-d68b-e2a3-3ee3bb0ec667', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:bd2181f5-b938-011b-7e44-81728310bdf5', 'Smart Searches', 'search.php?id=tbld%3A29925e0a-c825-0067-8882-db4b57866a96', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '80', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f8392545-41f4-d39a-da7e-9116c9a35502', 'Tabs', 'search.php?id=tbld%3A7e75af48-6f70-d157-f440-69a8e7f59d38', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '100', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:3620bdc0-edaa-ad59-8ac5-193f855a9584', 'Log Out', 'logout.php', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '10', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:2da14499-f301-9b18-e384-e0e73f06509e', 'Help', '', '', '200', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:113b56da-3722-6518-4c6a-7804d7ed0d19', 'About phpBMS', 'javascript:menu.showHelp()', 'menu:2da14499-f301-9b18-e384-e0e73f06509e', '0', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:08a6bb60-4557-b7d2-f2ba-09d828a1d9b2', 'Snapshot Widgets', 'search.php?id=tbld%3A2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'menu:bbc91ea7-d7e4-33b7-503e-5eb1b928f28b', '90', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:30bc9743-3530-7705-283a-d740b19238cf', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '20', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:13e03413-2f08-9b48-98a2-9bb83e4d15a1', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '900', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:83d23ec3-ad10-09e1-8c80-72de0c4747f9', '----', 'N/A', 'menu:1e23c59e-c429-fec5-cc94-99b53c4fc6b0', '920', 1, 1, NOW(), NOW(), '-100');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', 'Account', '', '', '5', 1, 1, NOW(), NOW(), '');
INSERT INTO `menu` (`uuid`, `name`, `link`, `parentid`, `displayorder`, `createdby`, `modifiedby`, `creationdate`, `modifieddate`, `roleid`) VALUES ('menu:e8401ebb-c369-304f-053d-8195988e7faf', '----', 'N/A', 'menu:f07d910f-f56d-3d24-e74f-7a3b36b2d3c8', '30', 1, 1, NOW(), NOW(), '-100');
--end menu INSERT--
--reports DELETE/INSERT--
DELETE FROM `reports` WHERE `name` IN ('Raw Table Print', 'Raw Table Export', 'Note Summary', 'SQL Export', 'Support Tables SQL Export');
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:37cee478-b57e-2d53-d951-baf3937ba9e0', 'Raw Table Print', 'report', '', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'report/general_tableprint.php', 'This report will prints out of every field for the table for the given records.  The report is displayed HTML format.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:dac75fb9-91d2-cb1e-9213-9fab6d32f4c8', 'Raw Table Export', 'export', '', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'report/general_export.php', 'This report will generate a comma-delimited text file. Values are encapsulated in quotes, and the first line lists the field names.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:2944b204-5967-348a-8679-6835f45f0d79', 'SQL Export', 'export', '', '0', 'Admin', 'report/general_sql.php', 'Generate SQL INSERT statements for records.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:37a299d1-d795-ad83-4b47-0778c16a381c', 'Support Tables SQL Export', 'export', 'tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3', '0', '', 'modules/base/report/tabledefs_sqlexport.php', 'Insert statements for all support table records for table definition records.', 1, NOW(), 1, NOW());
--end reports UPDATE--
--scheduler INSERT--
INSERT INTO `scheduler` (`uuid`, `name`, `job`, `crontab`, `lastrun`, `startdatetime`, `enddatetime`, `description`, `inactive`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('schd:fb52e7fb-bb49-7f5f-89e1-002b2785f085', 'Clean Import Files', './scheduler_delete_tempimport.php', '30::*::*::*::*', '2009-05-28 12:30:02', '2009-05-07 17:27:13', NULL, 'This will delete any temporary import files that are present (for whatever reason) after 30 minutes of their creation.', '0', 1, NOW(), 1, NOW());
INSERT INTO `scheduler` (`uuid`, `name`, `job`, `crontab`, `lastrun`, `startdatetime`, `enddatetime`, `description`, `inactive`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('schd:d1c247de-9811-d37f-ad94-a8472dc1bc9c', 'Remove Excess System Log Records', './scheduler_delete_logs.php', '*::24::*::*::*', NULL, '2009-03-31 12:00:00', NULL, 'This script will trim the system log when there are more than 2000 records present at the time of its calling (default will be every 24 hours).', '0', 1, NOW(), 1, NOW());
--end scheduler INSERT--
--settings INSERT--
INSERT INTO `settings` (`name`, `value`) VALUES ('application_uuid','');
INSERT INTO `settings` (`name`, `value`) VALUES ('auto_check_update','1');
INSERT INTO `settings` (`name`, `value`) VALUES ('send_metrics','0');
INSERT INTO `settings` (`name`, `value`) VALUES ('last_update_check','');
--end settings INSERT--
--smartsearches UPDATE--
UPDATE `smartsearches` SET `uuid`='smrt:ccc73fa4-6176-fad4-fbb1-5186d0edbdd1',`valuefield`='`users`.`uuid`' WHERE `id`='2';
UPDATE `smartsearches` SET `uuid`='smrt:855406d5-659d-c907-74a1-acfd3802fd73' WHERE `id`='5';
UPDATE `smartsearches` SET `uuid`='smrt:ed5b1d7f-b0fe-2088-f17c-47bfbe1ace25' WHERE `id`='9';
--end smartsearches UPDATE--
--tablecolumns INSERT--
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'widget', 'concat(\'[b]\', widgets.title, \'[/b][br]\', widgets.uuid)', 'left', '', '0', 'widgets.title', '0', '100%', 'bbcode', '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'role', 'IF(widgets.roleid != \'\', IF(widgets.roleid != \'Admin\', roles.name, \'Administrator\'), \'EVERYONE\')', 'left', '', '2', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'file', 'widgets.file', 'left', '', '1', '', '0', '', NULL, '');
DELETE FROM `tablecolumns` WHERE `tabledefid` = '19';
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'link', 'menu.link', 'left', '', '1', '', '1', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'access', 'IF(menu.roleid=\'\' OR menu.roleid IS NULL,\'EVERYONE\',if(menu.roleid=\'Admin\',\'Administrators\',roles.name)) ', 'left', '', '2', '', '0', '', NULL, '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'Item', 'IF(menu.parentid = \'\' OR menu.parentid IS NULL, CONCAT(\'[b]\', menu.name,\' [/b]\'), menu.name)', 'left', '', '0', '', '0', '100%', 'bbcode', '');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'display order', 'menu.displayorder', 'right', '', '3', '', '0', '', NULL, '');
--end tablecolumns INSERT--
--tablecolumns UPDATE--
UPDATE `tablecolumns` SET `column` = 'IF(`tabs`.`roleid`=\'\' OR `tabs`.`roleid` IS NULL,\'EVERYONE\',IF(`tabs`.`roleid`=\'Admin\',\'Administrators\',`roles`.`name`))' WHERE `column` = 'if(tabs.roleid=0,\'EVERYONE\',if(tabs.roleid=-100,\'Administrators\',roles.name))' AND `tabledefid` = '203';
UPDATE `tablecolumns` SET `column` = 'concat(\'[b]\', tabledefs.displayname, \'[/b][br][space][space]\', tabledefs.uuid)', `format` = 'bbcode' WHERE `name` = 'display' AND `tabledefid` = 11;
--end tablecolumns UPDATE--
--tabledefs INSERT--
INSERT INTO `tabledefs` (`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'Snapshot Widgets', 'wdgt', 'system', '1', 'widgets', '((`widgets` INNER JOIN `modules` ON `widgets`.`moduleid` = `modules`.`uuid`) LEFT JOIN `roles` ON `widgets`.`roleid` = `roles`.`uuid`)', 'modules/base/widgets_addedit.php', '-100', 'modules/base/widgets_addedit.php', '-100', NULL, '-100', '-100', '-100', '-100', 'delete', '0', '0', 'widgets.id != -1', 'widgets.title', NULL, NULL, NULL, 1, NOW(), 1, NOW());
DELETE FROM `tabledefs` WHERE `id` = '19';
INSERT INTO `tabledefs` (`id`,`uuid`, `displayname`, `prefix`, `type`, `moduleid`, `maintable`, `querytable`, `editfile`, `editroleid`, `addfile`, `addroleid`, `importfile`, `importroleid`, `searchroleid`, `advsearchroleid`, `viewsqlroleid`, `deletebutton`, `canpost`, `hascustomfields`, `defaultwhereclause`, `defaultsortorder`, `defaultsearchtype`, `defaultcriteriafindoptions`, `defaultcriteriaselection`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('19','tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'Menu', 'menu', 'system', '1', 'menu', '((menu LEFT JOIN menu as parentmenu on menu.parentid=parentmenu.uuid) LEFT JOIN roles on menu.roleid=roles.uuid)', 'modules/base/menu_addedit.php', '-100', 'modules/base/menu_addedit.php', '-100', NULL, 'Admin', '-100', '-100', '-100', 'delete', '0', '0', 'menu.id!=0', 'if(parentmenu.name is null,menu.displayorder,parentmenu.displayorder+(menu.displayorder+1)/10000)', '', '', '', 1, NOW(), 1, NOW());
--end tabledefs INSERT--
--tabledefs UPDATE--
UPDATE `tabledefs` SET `hascustomfields` = 1 WHERE `id` IN(12, 9, 26, 200);

UPDATE `tabledefs` SET
    `uuid` = 'tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb',
    `searchroleid` = -100,
    `addroleid` = -100,
    `editroleid` = -100,
    `prefix` = 'usr'
WHERE
    `id`='9';

UPDATE `tabledefs` SET
    `uuid`='tbld:8d19c73c-42fb-d829-3681-d20b4dbe43b9',
    `searchroleid` = -100,
    `addroleid` = -100,
    `editroleid` = -100,
    `prefix` = 'rln',
    `querytable` = '(`relationships` INNER JOIN `tabledefs` AS `fromtable` ON `relationships`.`fromtableid`=`fromtable`.`uuid`) INNER JOIN `tabledefs` AS `totable` ON `relationships`.`totableid`=`totable`.`uuid`'
WHERE
    `id`='10';

UPDATE `tabledefs` SET
    `uuid`='tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3',
    `prefix` = 'tbld',
    `querytable` = '`tabledefs` LEFT JOIN `modules` ON `tabledefs`.`moduleid` = `modules`.`uuid`'
WHERE
    `id`='11';

UPDATE `tabledefs` SET
    `uuid`='tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1',
    `prefix` = 'note'
WHERE
    `id`='12';

UPDATE `tabledefs` SET
    `uuid`='tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb',
    `searchroleid` = -100,
    `addroleid` = -100,
    `editroleid` = -100,
    `prefix` = 'rpt',
    `querytable` = '`reports` LEFT JOIN `tabledefs` ON `reports`.`tabledefid` = `tabledefs`.`uuid`'
WHERE
    `id`='16';

UPDATE `tabledefs` SET
    `uuid`='tbld:e251524a-2da4-a0c9-8725-d3d0412d8f4a',
    `searchroleid` = -100,
    `addroleid` = -100,
    `editroleid` = -100,
    `prefix` = 'sss',
    `querytable` = '(`usersearches` LEFT JOIN `users` ON `usersearches`.`userid` = `users`.`uuid`) INNER JOIN `tabledefs` ON `usersearches`.`tabledefid`=`tabledefs`.`uuid`'
WHERE
    `id`='17';

UPDATE `tabledefs` SET
    `uuid`='tbld:ea159d67-5e89-5b7f-f5a0-c740e147cd73',
    `searchroleid` = -100,
    `addroleid` = -100,
    `editroleid` = -100,
    `prefix` = 'mod'
WHERE
    `id`='21';

UPDATE `tabledefs` SET
    `uuid`='tbld:2bc3e683-81f9-694a-9550-a0c7263057de',
    `querytable` = '((`notes` LEFT JOIN `users` AS `assignedto` ON `assignedto`.`uuid` = `notes`.`assignedtoid`)  LEFT JOIN `users` as `assignedby` ON `assignedby`.`uuid`=`notes`.`assignedbyid`)'
WHERE
    `id`='23';

UPDATE `tabledefs` SET
    `uuid`='tbld:0fcca651-6c34-c74d-ac04-2d88f602dd71',
    `querytable` = '((`notes` LEFT JOIN `users` AS `assignedto` ON `assignedto`.`uuid` = `notes`.`assignedtoid`)  LEFT JOIN `users` as `assignedby` ON `assignedby`.`uuid`=`notes`.`assignedbyid`)'
WHERE
    `id`='24';

UPDATE `tabledefs` SET
    `uuid`='tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475',
    `prefix` = 'file'
WHERE
    `id`='26';

UPDATE `tabledefs` SET
    `uuid`='tbld:edb8c896-7ce3-cafe-1d58-5aefbcd5f3d7',
    `querytable` = '(`attachments` INNER JOIN `files` ON `attachments`.`fileid`=`files`.`uuid`)'
WHERE
    `id`='27';

UPDATE `tabledefs` SET
    `uuid`='tbld:87b9fe06-afe5-d9c6-0fa0-4a0f2ec4ee8a',
    `prefix` = 'role'
WHERE
    `id`='200';

UPDATE `tabledefs` SET
    `uuid`='tbld:83de284b-ef79-3567-145c-30ca38b40796',
    `prefix` = 'schd'
WHERE
    `id`='201';

UPDATE `tabledefs` SET
    `uuid`='tbld:3f71ab66-1f84-d68b-e2a3-3ee3bb0ec667',
    `querytable` = 'log LEFT JOIN users ON log.userid=users.uuid'
WHERE
    `id`='202';

UPDATE `tabledefs` SET
    `uuid`='tbld:7e75af48-6f70-d157-f440-69a8e7f59d38',
    `prefix` = 'tab',
    `querytable` = '`tabs` LEFT JOIN `roles` ON `tabs`.`roleid`=`roles`.`uuid`'
WHERE
    `id`='203';

UPDATE `tabledefs` SET
    `uuid`='tbld:29925e0a-c825-0067-8882-db4b57866a96',
    `prefix` = 'smsr',
    `querytable` = '(`smartsearches` INNER JOIN `tabledefs` ON `smartsearches`.`tabledefid` = `tabledefs`.`uuid`) INNER JOIN `modules` ON `smartsearches`.`moduleid` = `modules`.`uuid`'
WHERE
    `id`='204';

--end tabledefs UPDATE--
--tablefindoptions INSERST--
INSERT INTO `tablefindoptions` (`tabledefid`, `name`, `search`, `displayorder`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'All Records', 'widgets.id!=-1', '0', '');
--end tablefindoptions INSERT--
--tablefindoptions UPDATE--
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'TS\' AND `notes`.`private`=\'0\'' WHERE `tabledefid` = '23' AND `search` = 'notes.type=\'NT\' AND notes.private=0';
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'NT\' AND `notes`.`assignedbyid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='12' AND `search`='notes.type=\'NT\' and notes.assignedbyid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'NT\' AND `notes`.`assignedtoid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='12' AND `search`='notes.type=\'NT\' and notes.assignedtoid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'TS\' AND `notes`.`assignedbyid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='23' AND `search`='notes.type=\'TS\' and notes.assignedbyid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'TS\' AND `notes`.`assignedtoid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='23' AND `search`='notes.type=\'TS\' and notes.assignedtoid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
--Whereclause in the next update is NOT a typo.--
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'EV\' AND `notes`.`assignedbyid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='24' AND `search`='notes.type=\'NT\' and notes.assignedbyid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
UPDATE `tablefindoptions` SET `search` = '`notes`.`type`=\'EV\' AND `notes`.`assignedtoid`=\'{{$_SESSION[\'userinfo\'][\'id\']}}\' AND `notes`.`completed`=\'0\'' WHERE `tabledefid`='24' AND `search`='notes.type=\'EV\' and notes.assignedtoid={{$_SESSION[\'userinfo\'][\'id\']}} and notes.completed=0';
--end tablefindoptions UPDATE--
--tablegroupings INSERT--
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'modules.name', '1', '1', 'Module', '');
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'widgets.type', '2', '1', 'Area', '');
DELETE FROM `tablegroupings` WHERE `tabledefid` = '19';
INSERT INTO `tablegroupings` (`tabledefid`, `field`, `displayorder`, `ascending`, `name`, `roleid`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'if(menu.parentid=\'\' OR menu.parentid IS NULL,concat( lpad(menu.displayorder,3,\"0\"), \" - \" ,menu.name ) , concat( lpad(parentmenu.displayorder,3,\"0\") , \" - \",parentmenu.name))', '1', '1', '', '');
--end tablegroupings INSERT--
--tableoptions DELETE--
DELETE FROM `tableoptions` WHERE `name` = 'runSelected' AND `tabledefid` = '201' AND `option` = 'run job(s)';
--end tableoptions DELETE--
--tableoptions INSERT--
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:0fcca651-6c34-c74d-ac04-2d88f602dd71', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:29925e0a-c825-0067-8882-db4b57866a96', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2bc3e683-81f9-694a-9550-a0c7263057de', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:3f71ab66-1f84-d68b-e2a3-3ee3bb0ec667', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:7e75af48-6f70-d157-f440-69a8e7f59d38', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:83187e3d-101e-a8a5-037f-31e9800fed2d', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:83de284b-ef79-3567-145c-30ca38b40796', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:87b9fe06-afe5-d9c6-0fa0-4a0f2ec4ee8a', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:8d19c73c-42fb-d829-3681-d20b4dbe43b9', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:afe6d297-b484-4f0b-57d4-1c39412e9dfb', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:d595ef42-db9d-2233-1b9b-11dfd0db9cbb', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:e251524a-2da4-a0c9-8725-d3d0412d8f4a', 'import', '1', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:ea159d67-5e89-5b7f-f5a0-c740e147cd73', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:edb8c896-7ce3-cafe-1d58-5aefbcd5f3d7', 'import', '0', '0', '0', 'Admin', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'new', '1', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'edit', '1', '1', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'printex', '1', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'select', '1', '0', '0', '', '0');
INSERT INTO `tableoptions` (`tabledefid`, `name`, `option`, `needselect`, `othercommand`, `roleid`, `displayorder`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'import', '1', '0', '0', 'Admin', '0');
--end tableoptions INSERT--
--tableoptions UPDATE--
UPDATE `tableoptions` SET `needselect` = 0 WHERE `name` IN('massEmail','new','printex','select') AND `tabledefid` IN (9,10,11,12,16,17,19,21,23,24,26,27,200,201,202,203,204);
--end tableoptions UPDATE--
--tablesearchablefields INSERT--
INSERT INTO `tablesearchablefields` (`tabledefid`, `field`, `name`, `displayorder`, `type`) VALUES ('tbld:2ad5146c-d4c0-db8e-592a-c0cc2f3c2c21', 'widgets.id', 'id', '1', 'field');
--end tablesearchablefields INSERT--
--tabs INSERT--
INSERT INTO `tabs` (`uuid`, `name`, `tabgroup`, `location`, `displayorder`, `enableonnew`, `roleid`, `tooltip`, `notificationsql`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('tab:2ebf956d-5e39-c7d5-16b7-501b64685a5a', 'custom fields', 'tabledefs entry', 'modules/base/tabledefs_custom.php', '60', '0', '-100', NULL, NULL, 1, NOW(), 1, NOW());
--end tabs INSERT--
--tabs UPDATE--
UPDATE `tabs` SET `uuid`='tab:fdf064e0-f2d9-6c67-b64f-449e72e859b9' WHERE `id`='1';
UPDATE `tabs` SET `uuid`='tab:b1011143-1d47-520e-5879-3953a4f5055b' WHERE `id`='2';
UPDATE `tabs` SET `uuid`='tab:c5bdaf10-062c-fb3a-f40f-ddce821fd579' WHERE `id`='3';
UPDATE `tabs` SET `uuid`='tab:276dacd4-4a37-d979-aeda-a7982f632559' WHERE `id`='4';
UPDATE `tabs` SET `uuid`='tab:22d08e82-5047-4150-6de7-49e89149f56b' WHERE `id`='5';
UPDATE `tabs` SET `uuid`='tab:c111eaf5-692b-9c7d-1d46-1bacb6703361' WHERE `id`='100';
--end tabs UPDATE--
--users UPDATE--
UPDATE `users` SET `admin`='1' WHERE `portalaccess`='1';
UPDATE `users` SET `uuid`='usr:5c196e01-193a-8952-fee7-29b4e5e6a0b0' WHERE `id`='1';
UPDATE `users` SET `uuid`='usr:cb67a60b-a264-735c-6189-49a7c883af0b' WHERE `id`='2';
UPDATE `users` SET `uuid`='usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75' WHERE `id`='-2';
--end users UPDATE--
--widgets INSERT--
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:a1aec114-954b-37c1-0474-7d4e851c728c', 'little', 'Workload', 'widgets/workload/class.php', '', 'mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:13d228d3-bbee-e7d2-6571-83a568688e3d', 'big', 'Events', 'widgets/events/class.php', '', 'mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7', '1', 1, NOW(), 1, NOW());
INSERT INTO `widgets` (`uuid`, `type`, `title`, `file`, `roleid`, `moduleid`, `default`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('wdgt:bc323640-6497-cb6f-5897-029af7dcb3c9', 'little', 'System Statistics', 'widgets/systemstats/class.php', 'Admin', 'mod:29873ee8-c12a-e3f6-9010-4cd24174ffd7', '0', 1, NOW(), 1, NOW());
--end widgets INSERT--
