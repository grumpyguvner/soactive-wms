INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561','name','if(stylecategories.description, concat(\'[b]\', stylecategories.name,\'[/b][br]\',stylecategories.description), concat(\'[b]\', stylecategories.name,\'[/b]\'))','left','',1,'stylecategories.name',0,'100%','bbcode','');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561','parent category','if(isnull(parents.uuid), \'No Parent\', parents.name)','left','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561','attractive','if(isnull(attractive_categories.id), \'None\', attractive_categories.name)','left','',3,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561','display order','stylecategories.displayorder','right','',4,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:3342a3d4-c6a2-3a38-6576-419299859561','web','stylecategories.webenabled','center','',0,'',0,'','boolean','');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:7ecb8e4e-8301-11df-b557-00238b586e42','style number','styles.stylenumber','left',NULL,0,NULL,0,'',NULL,NULL);
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:7ecb8e4e-8301-11df-b557-00238b586e42','name','styles.stylename','left',NULL,1,NULL,1,'100%',NULL,NULL);
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:7ecb8e4e-8301-11df-b557-00238b586e42','status','styles.status','left',NULL,3,NULL,0,'',NULL,NULL);
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:7ecb8e4e-8301-11df-b557-00238b586e42','unit price','styles.unitprice','right',NULL,4,NULL,0,'','currency',NULL);
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:7ecb8e4e-8301-11df-b557-00238b586e42','type','styles.type','left',NULL,2,NULL,0,'',NULL,NULL);
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:863abaff-9673-d0cc-386d-695195f3e471','priority','sizes.priority','right','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:863abaff-9673-d0cc-386d-695195f3e471','name','sizes.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:863abaff-9673-d0cc-386d-695195f3e471','bleep id','sizes.bleepid','right','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:433db989-89d5-dddf-2e8f-ea094862a1c4','priority','colours.priority','right','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:433db989-89d5-dddf-2e8f-ea094862a1c4','name','colours.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:433db989-89d5-dddf-2e8f-ea094862a1c4','bleep id','colours.bleepid','right','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411','priority','groups.priority','right','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411','name','groups.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411','bleep id','groups.bleepid','right','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:01f631cd-9eec-cc24-3d34-d615940897ab','priority','departments.priority','right','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:01f631cd-9eec-cc24-3d34-d615940897ab','name','departments.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:01f631cd-9eec-cc24-3d34-d615940897ab','bleep id','departments.bleepid','right','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82','priority','suppliers.priority','right','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82','name','suppliers.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82','bleep id','suppliers.bleepid','right','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:85867a3d-df59-ed27-4830-370fd5a1493b','id','products.id','left','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:62c4b57e-5adc-951e-a0f8-e1ee05b99e43','name','locations.name','left','',1,'',0,'99%',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:62c4b57e-5adc-951e-a0f8-e1ee05b99e43','priority','locations.priority','left','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:62c4b57e-5adc-951e-a0f8-e1ee05b99e43','bleep table','bleepid','left','',2,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb','lanugage','styles_translations.site','left','',0,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb','style name','styles_translations.stylename','left','',1,'',0,'',NULL,'');
INSERT INTO `tablecolumns` (`tabledefid`, `name`, `column`, `align`, `footerquery`, `displayorder`, `sortorder`, `wrap`, `size`, `format`, `roleid`) VALUES ('tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb','description','styles_translations.description','left','',2,'',1,'',NULL,'');
