CREATE TABLE `bleep_emails` (
  `id` int(11) NOT NULL auto_increment,

  `msgno` int(11) NOT NULL,

  `importeddate` timestamp(14) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processeddate` timestamp(14),

  PRIMARY KEY (`id`)
);