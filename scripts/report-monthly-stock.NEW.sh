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

mysql -u'activewms' -p'St4rl!ght' -e 'SELECT su.name AS brand, p.supplierref, p.bleepid AS plu, s.stylenumber, s.stylename AS name, c.name AS colour, sz.name AS size, p.bleep_webstore AS warehouse, s.unitcost AS cost, s.unitprice AS retail, s.saleprice AS sale, s.season, IFNULL((SELECT g.name FROM genders g WHERE g.uuid = s.genderid),"not set") AS gender, IFNULL((SELECT pt.name FROM producttypes pt WHERE pt.uuid = s.producttypeid),"not set") AS producttype, IFNULL((SELECT sp.name FROM sports sp WHERE sp.uuid = s.default_sportid),"not set") AS sport, IFNULL((SELECT ct.name FROM categories ct WHERE ct.uuid = s.default_categoryid),"not set") AS category FROM products p JOIN colours c ON p.colourid = c.uuid JOIN sizes sz ON p.sizeid = sz.uuid JOIN styles s ON p.styleid = s.uuid JOIN suppliers su ON s.supplierid = su.uuid WHERE p.bleep_webstore <> 0;' activewms >> ${EMAILMESSAGE}

echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
mail -s "${EMAILSUBJECT}" "reports@soactive.com" < ${EMAILMESSAGE}
# mail -s "${EMAILSUBJECT}" "mark.horton@totallyboundless.com" < ${EMAILMESSAGE}
/bin/rm ${EMAILMESSAGE}

exit 0 #Exit normally
