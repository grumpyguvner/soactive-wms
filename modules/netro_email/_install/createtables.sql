CREATE TABLE `netro_emails` (
  `id` int(11) NOT NULL auto_increment,

  `clientid` VARCHAR(64) default NULL,

  `channel` VARCHAR(64) default NULL,
  `orderreference` VARCHAR(64) default NULL,
  `orderdate` VARCHAR(64) default NULL,

  `customername` VARCHAR(64) default NULL,
  `customeraddress` VARCHAR(64) default NULL,
  `customertown` VARCHAR(64) default NULL,
  `customerpostcode` VARCHAR(64) default NULL,
  `customercountry` VARCHAR(64) default NULL,
  `customertelephone` VARCHAR(64) default NULL,
  `customeremail` VARCHAR(64) default NULL,

  `deliveryname` VARCHAR(64) default NULL,
  `deliveryaddress` VARCHAR(64) default NULL,
  `deliverytown` VARCHAR(64) default NULL,
  `deliverypostcode` VARCHAR(64) default NULL,
  `deliverycountry` VARCHAR(64) default NULL,

  `ordersubtotal` VARCHAR(64) default NULL,
  `deliveryregion` VARCHAR(64) default NULL,
  `deliverytype` VARCHAR(64) default NULL,
  `deliverycost` VARCHAR(64) default NULL,
  `ordertotal` VARCHAR(64) default NULL,

  `paymentmethod` VARCHAR(64) default NULL,
  `paymentreference` VARCHAR(64) default NULL,
  `paymentresult` VARCHAR(64) default NULL,
  `paymentcurrency` VARCHAR(64) default NULL,
  `paymenttotal` VARCHAR(64) default NULL,
  `paymentauthorisation` VARCHAR(64) default NULL,
  `fraudchecks` VARCHAR(64) default NULL,
  `fraudcheckaddress` VARCHAR(64) default NULL,
  `fraudcheckpostcode` VARCHAR(64) default NULL,
  `fraudcheckcv2` VARCHAR(64) default NULL,

  `importeddate timestamp(14) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processeddate timestamp(14) default NULL,

  PRIMARY KEY `theid` (`id`),
  KEY `orderref` (`orderreference`)
  KEY `client` (`clientid`)
)  ENGINE=INNODB;

CREATE TABLE netro_lineitems (
  id int(11) NOT NULL auto_increment,
  emailid int(11) NOT NULL default '0',

  `productid` VARCHAR(64) default NULL,

  `linetext` VARCHAR(255) default NULL,

  `importeddate timestamp(14) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processeddate timestamp(14) default NULL,

  PRIMARY KEY `theid` (id),
  KEY email (emailid),
  KEY product (productid)
) ENGINE=INNODB;
