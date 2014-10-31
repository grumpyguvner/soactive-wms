INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:37cee478-b57e-2d53-d951-baf3937ba9e0', 'Raw Table Print', 'report', '', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'report/general_tableprint.php', 'This report will prints out of every field for the table for the given records.  The report is displayed HTML format.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:dac75fb9-91d2-cb1e-9213-9fab6d32f4c8', 'Raw Table Export', 'export', '', '0', 'role:259ead9f-100b-55b5-508a-27e33a6216bf', 'report/general_export.php', 'This report will generate a comma-delimited text file. Values are encapsulated in quotes, and the first line lists the field names.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:2944b204-5967-348a-8679-6835f45f0d79', 'SQL Export', 'export', '', '0', 'Admin', 'report/general_sql.php', 'Generate SQL INSERT statements for records.', 1, NOW(), 1, NOW());
INSERT INTO `reports` (`uuid`, `name`, `type`, `tabledefid`, `displayorder`, `roleid`, `reportfile`, `description`, `createdby`, `creationdate`, `modifiedby`, `modifieddate`) VALUES ('rpt:37a299d1-d795-ad83-4b47-0778c16a381c', 'Support Tables SQL Export', 'export', 'tbld:5c9d645f-26ab-5003-b98e-89e9049f8ac3', '0', '', 'modules/base/report/tabledefs_sqlexport.php', 'Insert statements for all support table records for table definition records.', 1, NOW(), 1, NOW());
