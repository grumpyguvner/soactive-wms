#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/activewms/data_transfers/feeds

# Generate & upload the kelkoo feed
mysql -e "SELECT kelkoo_crossreference.kelkoo_category AS 'Category',
	kelkoo_crossreference.kelkoo_type AS 'Type',
	suppliers.name AS 'Manufacturer',
	styles.description AS 'Title',
	NULL AS 'Field_E',
	NULL AS 'Field_F',
	NULL AS 'Field_G',
	NULL AS 'Field_H',
	NULL AS 'Field_I',
	NULL AS 'Field_J',
	'' AS 'EAN',
	products.bleepid AS 'SKU',
	left(styles.webdescription,10000) AS 'Product Description',
	'' AS 'Promotional Description',
	concat(_latin1'http://www.sheactive.co.uk/images/items/338x451/IMG',styles.stylenumber,lpad(colours.bleepid,4,_latin1'0'),_latin1'.jpg') AS 'Image',
	concat(_latin1'http://www.sheactive.co.uk/item_detail.html?&itemno=',lpad(styles.stylenumber,4,_latin1'0'),_latin1'&type=brand&group=',lpad(suppliers.bleepid,8,_latin1'0'),_latin1'&utm_source=kelkoo&utm_medium=',lpad(styles.stylenumber,4,_latin1'0')) AS 'Link',
	IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) AS 'Price',
	0 AS 'Delivery Cost',
	'2-3 Days' AS 'Delivery Time',
	'In Stock' AS 'Availability',
	'' AS 'Warranty',
	'New' AS 'Condition',
	'' AS 'Offer Type',
	0.01 AS 'Bid'

FROM	products
	join colours on (products.colourid=colours.uuid)
	join sizes on (products.sizeid=sizes.uuid)
	join styles on (products.styleid=styles.uuid)
	join departments on (styles.categoryid=departments.categoryid)
	join kelkoo_crossreference on (departments.bleepid=kelkoo_crossreference.dept_id)
	join suppliers on (styles.supplierid=suppliers.uuid)

WHERE (products.available_stock > 1) AND (IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) > 20)" --host=localhost -u'activewms' -p'St4rl!ght' activewms > kelkoo.txt

wput --reupload kelkoo.txt ftp://sheactive:NicNigvic3@ftpkelkoo.kelkoo.net
