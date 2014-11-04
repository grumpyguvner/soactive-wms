#!/bin/bash
#
# Script to export stock list to Brighton
#

echo "Begin processing ..."

EMAILSUBJECT="Warehouse Stock List Update"
EMAILFILE="/tmp/`date +%s`-stocklist.csv"
EMAILFILE=`mktemp`
EMAILMESSAGE="/tmp/`date +%s`-message"
EMAILMESSAGE=`mktemp`

mysql -u activewms -p'St4rl!ght' -e 'SELECT s.season AS "Season", su.name AS "Brand", s.stylenumber AS "Style", s.stylename AS "Name", c.name AS "Colour", sz.name AS "Size", p.bleepid AS "PLU", p.bleep_webstore AS "Stock", s.unitcost AS "Wholesale £", s.unitprice AS "Retail £", IF(IFNULL(s.saleprice,0)<>0,s.saleprice,s.unitprice) AS "Price £", FORMAT((styles_translations.price*1.196),2) AS "Retail €" FROM products p JOIN styles s ON (p.styleid = s.uuid) JOIN colours c ON (p.colourid = c.uuid) JOIN sizes sz ON (p.sizeid = sz.uuid) JOIN suppliers su ON (s.supplierid = su.uuid) JOIN styles_translations ON (styles_translations.styleid = s.uuid and styles_translations.site = "www.attractive.fr") WHERE p.bleep_brighton > 0 OR p.bleep_webstore > 0 ORDER BY s.season, su.name, s.stylenumber, c.bleepid, sz.bleepid;' activewms > ${EMAILFILE}

echo "Warehouse Stock List at `date`" > ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
cat ${EMAILFILE} >> ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
# (cat ${EMAILMESSAGE};cat ${EMAILFILE}) | mail -s "${EMAILSUBJECT}" "mark.horton@boundlesscommerce.co.uk"
(cat ${EMAILMESSAGE}) | mail -s "${EMAILSUBJECT}" "reports@soactive.com"
/bin/rm ${EMAILMESSAGE}
/bin/rm ${EMAILFILE}

exit 0 #Exit normally
