#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/activewms/data_transfers/feeds

# Generate & uplaod the froogle feed
mysql -e "SELECT suppliers.name AS brand,
	'new' AS 'condition',
	left(styles.webdescription,10000) AS description,
	DATE_ADD(CURDATE(),INTERVAL 30 DAY) AS expiration_date,
	products.bleepid AS id,
	concat(_latin1'http://www.sheactive.co.uk/images/items/338x451/IMG',styles.stylenumber,lpad(colours.bleepid,4,_latin1'0'),_latin1'.jpg') AS image_link,
	concat(_latin1'http://www.sheactive.co.uk/item_detail.html?&itemno=',lpad(styles.stylenumber,4,_latin1'0'),_latin1'&type=brand&group=',lpad(suppliers.bleepid,8,_latin1'0'),_latin1'&utm_source=froogle&utm_medium=',lpad(styles.stylenumber,4,_latin1'0')) AS link,
	IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) AS price,
	'sporting goods' AS product_type,
	concat(styles.description, ' ', colours.name, ' ', sizes.name) AS title,
	colours.name AS colour,
	sizes.name AS size,
	'Visa, Mastercard' AS payment_accepted,
	'Visa, Mastercard, Delta, Maestro, Switch, Solo. All credit cards are processed via the SagePay online card processing service.' AS payment_notes,
	'false' AS pickup,
	'fixed' AS price_type 

FROM	products
	join colours on (products.colourid=colours.uuid)
	join sizes on (products.sizeid=sizes.uuid)
	join styles on (products.styleid=styles.uuid)
	join suppliers on (styles.supplierid=suppliers.uuid)

WHERE (products.available_stock > 1)" --host=localhost -u'activewms' -p'St4rl!ght' activewms > froogle_new.txt
# wput --reupload froogle_new.txt ftp://froogle.sheactive:froogle98@sheactive.net/
# Lets try the google upload server!
wput --reupload froogle_new.txt ftp://sheactive:shesh1ne@uploads.google.com/
# Froogle will only accept feeds from same domain as website!
# wput --reupload froogle_new.txt ftp://sheactiveData:shedata50@sheactive.co.uk/
