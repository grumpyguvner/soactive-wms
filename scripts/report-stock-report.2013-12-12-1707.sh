#!/bin/bash
#
# Script to generate report for Monthly stock valuation
#
# This module assumes that it is being executed on the 1st 
#

EMAILSUBJECT="Monthly Stock Valuation"
EMAILMESSAGE="/tmp/`date +%s`-message"
EMAILMESSAGE=`mktemp`

echo "Stock valuation at `date +%s`" > ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}

mysql -u'activewms' -p'St4rl!ght' --column-names -e 'SELECT DISTINCT suppliers.name as "brand", styles.stylenumber as "style number", styles.stylename as "description", styles.season as "season", IFNULL(styles.quantity_received,0) as "qty received", styles.available_stock as "available (WEB)", styles.brighton_stock as "available (BRIGHTON)", (styles.available_stock+styles.brighton_stock) as "TOTAL", styles.unitcost as "cost price (£) ex VAT", (styles.available_stock+styles.brighton_stock)*styles.unitcost as "value", styles.unitprice as "retail", IF(IFNULL(styles.saleprice,0)<>0,styles.saleprice,styles.unitprice) as "current sale price", IFNULL(styles.quantity_sold,0) as "qty sold" FROM styles JOIN suppliers ON (suppliers.uuid = styles.supplierid) WHERE styles.id!=0 ORDER BY suppliers.name, suppliers.name, styles.stylename;' activewms >> ${EMAILMESSAGE}

echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
mail -s "${EMAILSUBJECT}" "reports@soactive.com" < ${EMAILMESSAGE}
# mail -s "${EMAILSUBJECT}" "mark@sheactive.net" < ${EMAILMESSAGE}
/bin/rm ${EMAILMESSAGE}

exit 0 #Exit normally
