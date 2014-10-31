#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/activewms/data_transfers/feeds

# Generate & uplaod the shopzilla feed
mysql -e "SELECT shopzilla_crossreference.category_id AS 'Category',
	suppliers.name AS 'Manufacturer',
	styles.description AS 'Title',
	left(styles.webdescription,10000) AS 'Product Description',
	concat(_latin1'http://www.sheactive.co.uk/item_detail.html?&itemno=',lpad(styles.stylenumber,4,_latin1'0'),_latin1'&type=brand&group=',lpad(suppliers.bleepid,8,_latin1'0'),_latin1'&utm_source=shopzilla&utm_medium=',lpad(styles.stylenumber,4,_latin1'0')) AS 'Link',
	concat(_latin1'http://www.sheactive.co.uk/images/items/338x451/IMG',styles.stylenumber,lpad(colours.bleepid,4,_latin1'0'),_latin1'.jpg') AS 'Image',
	products.bleepid AS 'SKU',
	'In Stock' AS 'Stock',
	'New' AS 'Condition',
	NULL AS 'Shipping Weight',
	0 AS 'Shipping Cost',
	NULL AS 'Bid',
	'' AS 'Promotional Description',
	'' AS 'EAN',
	IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) AS 'Price'

FROM	products
	join colours on (products.colourid=colours.uuid)
	join sizes on (products.sizeid=sizes.uuid)
	join styles on (products.styleid=styles.uuid)
	join departments on (styles.categoryid=departments.categoryid)
	join shopzilla_crossreference on (departments.bleepid=shopzilla_crossreference.dept_id)
	join suppliers on (styles.supplierid=suppliers.uuid)

WHERE (products.available_stock > 1) AND (IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) > 20)" --host=localhost -u'activewms' -p'St4rl!ght' activewms > shopzilla.txt

wput --reupload shopzilla.txt ftp://shopzilla.sheactive:shopzilla99@ftp.sheactive.net/