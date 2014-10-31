#!/bin/bash
#
# Script to export stock list to Brighton
#

echo "Begin processing ..."

EMAILSUBJECT="Stock List Update"
EMAILFILE="/tmp/`date +%s`-stocklist.csv"
EMAILFILE=`mktemp`
EMAILMESSAGE="/tmp/`date +%s`-message"
EMAILMESSAGE=`mktemp`

mysql -u activewms -p'St4rl!ght' -e 'SELECT su.name, s.stylenumber, s.stylename, c.name AS colour, sz.name AS size, p.bleepid, p.bleep_brighton AS brighton, p.bleep_webstore AS warehouse, s.season FROM products p JOIN styles s ON (p.styleid = s.uuid) JOIN colours c ON (p.colourid = c.uuid) JOIN sizes sz ON (p.sizeid = sz.uuid) JOIN suppliers su ON (s.supplierid = su.uuid) WHERE p.bleep_brighton <> 0 OR p.bleep_webstore <> 0 ORDER BY s.season, su.name, s.stylenumber, c.bleepid, sz.bleepid;' activewms > ${EMAILFILE}

echo "Stock List at `date`" > ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
cat ${EMAILFILE} >> ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
# (cat ${EMAILMESSAGE};cat ${EMAILFILE}) | mail -s "${EMAILSUBJECT}" "datatransfers@sheactive.net"
(cat ${EMAILMESSAGE}) | mail -s "${EMAILSUBJECT}" "datatransfers@sheactive.net"
/bin/rm ${EMAILMESSAGE}
/bin/rm ${EMAILFILE}

exit 0 #Exit normally
