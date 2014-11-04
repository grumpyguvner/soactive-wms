#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/activewms/data_transfers/feeds

# Generate & upload the pricerunner feed
mysql -e "SELECT products.bleepid AS 'SKU',
	IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) AS 'PRICE',
	suppliers.name AS 'MANUFACTURER',
	styles.description AS 'PRODUCT NAME',
	pricerunner_crossreference.pricerunner_category AS 'CATEGORY',
	concat(_latin1'http://www.sheactive.co.uk/item_detail.html?&itemno=',lpad(styles.stylenumber,4,_latin1'0'),_latin1'&type=brand&group=',lpad(suppliers.bleepid,8,_latin1'0'),_latin1'&utm_source=pricerunner&utm_medium=',lpad(styles.stylenumber,4,_latin1'0')) AS 'URL',
	concat(_latin1'http://www.sheactive.co.uk/images/items/338x451/IMG',styles.stylenumber,lpad(colours.bleepid,4,_latin1'0'),_latin1'.jpg') AS 'Graphic URL',
	0 AS 'SHIPPING COST',
	'In Stock' AS 'Stock Options',
	'2-3 Days' AS 'Delivery Time',
	left(styles.webdescription,10000) AS 'Description',
	'' AS 'EAN',
	'' AS 'Unique Retailer Message',
	'' AS 'PRID'

FROM	products
	join colours on (products.colourid=colours.uuid)
	join sizes on (products.sizeid=sizes.uuid)
	join styles on (products.styleid=styles.uuid)
	join departments on (styles.categoryid=departments.categoryid)
	join pricerunner_crossreference on (departments.bleepid=pricerunner_crossreference.dept_id)
	join suppliers on (styles.supplierid=suppliers.uuid)

WHERE (products.available_stock > 1) AND (IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) > 20)" --host=localhost -u'activewms' -p'St4rl!ght' activewms > pricerunner.txt

wput --reupload pricerunner.txt ftp://pricerunner.sheactive:yabscp34@ftp.sheactive.net/
