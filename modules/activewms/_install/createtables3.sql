DROP TABLE IF EXISTS `subscriptions_updates`;
CREATE TABLE `subscriptions_updates` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(128) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  `status` varchar(128) DEFAULT NULL,
  `modifieddate` timestamp(14) NOT NULL,
  `requested` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `crmupdated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `type` (`type`)
)  ENGINE=INNODB DEFAULT CHARSET=utf8;
