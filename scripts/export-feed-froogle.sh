#!/bin/bash
#
# Script to generate and export feeds from database
#

cd /home/sheactive/feeds

# Generate & uplaod the froogle feed
mysql -e "SELECT b.name AS brand,
        'new' AS 'condition',
        left(sl.description,10000) AS description,
        DATE_ADD(CURDATE(),INTERVAL 30 DAY) AS expiration_date,
        p.reference AS id,
        concat('http://www.sheactive.co.uk/img/p/',s.id_product,'-',i.id_image,'-large.jpg') AS image_link,
        concat('http://www.sheactive.co.uk/',s.reference,'&utm_source=froogle&utm_campaign=',s.reference) AS link,
        s.price AS price,
        'sporting goods' AS product_type,
        sl.name AS title,
        '' AS colour,
        al.name AS size,
        'Visa, Mastercard, Paypal' AS payment_accepted,
        'Visa, Mastercard, Delta, Maestro, Switch, Solo. All credit cards are processed via the SagePay online card processing service.' AS payment_notes,
        'false' AS pickup,
        'fixed' AS price_type 

FROM  (((ps_product_attribute p
        join ps_product_attribute_combination pac on (p.id_product_attribute=pac.id_product_attribute)
        join ps_attribute_lang al on (pac.id_attribute=al.id_attribute and al.id_lang=1))
        join ps_product s on (p.id_product=s.id_product)
        join ps_product_lang sl on (p.id_product=sl.id_product AND sl.id_lang=1))
        join ps_manufacturer b on (s.id_manufacturer=b.id_manufacturer))
        join ps_image i on (s.id_product=i.id_product AND i.cover=1)

WHERE (p.quantity > 1)
" --host=localhost -u'sheactive' -p'St4rl!ght' sheactive > froogle_new.txt
# wput --reupload froogle_new.txt ftp://froogle.sheactive:froogle98@sheactive.net/
# Lets try the google upload server!
wput --reupload froogle_new.txt ftp://sheactive:shesh1ne@uploads.google.com/
# Froogle will only accept feeds from same domain as website!
# wput --reupload froogle_new.txt ftp://sheactiveData:shedata50@sheactive.co.uk/
