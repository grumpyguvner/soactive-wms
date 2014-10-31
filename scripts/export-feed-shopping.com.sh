#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/activewms/data_transfers/feeds

# Generate & upload the shopping.com feed
mysql -e "SELECT products.bleepid AS 'Merchant SKU',
	'' AS 'MPN',
	products.bleepid AS 'UPC',
	suppliers.name AS 'Brand',
	styles.description AS 'Product Name',
	concat(_latin1'http://www.sheactive.co.uk/item_detail.html?&itemno=',lpad(styles.stylenumber,4,_latin1'0'),_latin1'&type=brand&group=',lpad(suppliers.bleepid,8,_latin1'0'),_latin1'&utm_source=shoppingcom&utm_medium=',lpad(styles.stylenumber,4,_latin1'0')) AS 'Product URL',
	IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) AS 'Price',
	31515 AS 'Category ID',
	'Women' AS 'Gender',
	concat(_latin1'http://www.sheactive.co.uk/images/items/338x451/IMG',styles.stylenumber,lpad(colours.bleepid,4,_latin1'0'),_latin1'.jpg') AS 'Image URL',
	left(styles.webdescription,10000) AS 'Product Description',
	'In Stock' AS 'Stock Description',
	'New' AS 'Condition',
	'Yes' AS 'Stock Availability',
	0 AS 'Shipping Rate',
	'2-3 Days' AS 'Estimated P&P Date'

FROM	products
	join colours on (products.colourid=colours.uuid)
	join sizes on (products.sizeid=sizes.uuid)
	join styles on (products.styleid=styles.uuid)
	join suppliers on (styles.supplierid=suppliers.uuid)

WHERE (products.available_stock > 1) AND (IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) > 20)" --host=localhost -u'activewms' -p'St4rl!ght' activewms > shoppingcom.txt

wput --reupload shoppingcom.txt ftp://m477452:nFjUfv7X@ftp.shopping.com/
