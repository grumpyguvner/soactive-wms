#!/bin/bash
#
# Script to export attractive data
#

echo "Begin processing ..."

EMAILSUBJECT="attractive.fr data export"
EMAILFILE="/tmp/`date +%s`-attractive-data.csv"
EMAILFILE=`mktemp`
EMAILMESSAGE="/tmp/`date +%s`-message"
EMAILMESSAGE=`mktemp`

mysql -u activewms -p'St4rl!ght' -e 'SELECT DISTINCT styles_translations.id as theid,lpad(s.stylenumber,5,"0") as "style ref", suppliers.name as "supplier", styles_translations.stylename as "style name", styles_translations.description as "description", styles_translations.webdescription as "long description", CONCAT(FORMAT((styles_translations.wholesale_price),2)," €") as "wholesale", CONCAT(FORMAT((styles_translations.price*1.196),2)," €") as "price", CONCAT(FORMAT((styles_translations.reduction_price),2)," €") as "reduction €", CONCAT(FORMAT((styles_translations.reduction_percent),1)," %") as "reduction %", s.stylename as "uk style name", s.description as "uk description", s.webdescription as "uk long description", CONCAT("£ ",FORMAT((s.unitcost),2)) as "uk wholesale", CONCAT("£ ",FORMAT((s.unitprice),2)) as "uk price", CONCAT("£ ",FORMAT((s.saleprice),2)) as "uk sale price", s.available_stock as "stock", s.season as "season", s.creationdate as "style added" FROM styles_translations left join styles s on (styles_translations.styleid = s.uuid and styles_translations.site = "www.attractive.fr") left join suppliers on s.supplierid = suppliers.uuid WHERE styles_translations.site = "www.attractive.fr" AND styles_translations.inactive=0 ORDER BY s.stylenumber DESC;' activewms > ${EMAILFILE}

echo "Attractive Data Extract at `date`" > ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
cat ${EMAILFILE} >> ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
# (cat ${EMAILMESSAGE};cat ${EMAILFILE}) | mail -s "${EMAILSUBJECT}" "reports@soactive.com"
(cat ${EMAILMESSAGE}) | mail -s "${EMAILSUBJECT}" "reports@soactive.com"
/bin/rm ${EMAILMESSAGE}
/bin/rm ${EMAILFILE}

exit 0 #Exit normally
