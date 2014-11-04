#!/bin/bash
source ./pid.sh
source ./log.sh

# Ensure all style default category is in the category list 
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestostylecategories (styleid, stylecategoryid) SELECT s.uuid, s.categoryid FROM styles s LEFT JOIN stylestostylecategories sc ON (s.uuid = sc.styleid AND s.categoryid = sc.stylecategoryid) WHERE ISNULL(sc.stylecategoryid) AND NOT ISNULL(s.categoryid);'
# Reset the reserved stock figures according to the last pick list
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE products p SET p.reserved_stock=0;'
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE orders o LEFT JOIN orderitems i ON (i.orderid = o.uuid) JOIN products p ON (i.upc = p.bleepid) SET p.reserved_stock = (p.reserved_stock + i.quantity) WHERE o.orderdate >= date_sub(now(),interval 72 hour);'
# Update Stock Sold at Style Level
# mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles SET quantity_sold = (SELECT SUM(quantity_sold) FROM productsalesbylocation WHERE productsalesbylocation.styleid = styles.uuid)'
# Update Stock Received at Style Level
# mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles SET quantity_received = (SELECT SUM(quantity_received) FROM productreceiptsbylocation WHERE productreceiptsbylocation.styleid = styles.uuid)'
# Update Available Stock at Style Level
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles SET available_stock = (SELECT SUM(available_stock) FROM products WHERE products.styleid = styles.uuid), reserved_stock = (SELECT SUM(reserved_stock) FROM products WHERE products.styleid = styles.uuid)'
# Update Total Warehouse Stock at Style Level
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles SET warehouse_stock = (SELECT SUM(bleep_webstore) FROM products WHERE products.styleid = styles.uuid)'
# Update Total Brighton Stock at Style Level
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles SET brighton_stock = (SELECT SUM(bleep_brighton) FROM products WHERE products.styleid = styles.uuid)'
# Make sure that the style modifieddate reflects changes to the images
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles s SET s.modifieddate = (SELECT MAX(i.modifieddate) FROM styles_images i WHERE (i.styleid = s.uuid)) WHERE s.modifieddate < (SELECT MAX(i.modifieddate) FROM styles_images i WHERE (i.styleid = s.uuid))'
# Deactive styles more than a year old that have no stock
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles s SET s.webenabled = 0 WHERE s.creationdate < NOW() - INTERVAL 1 YEAR AND s.webenabled = 1 AND s.available_stock = 0'

exit 0 #Exit normally