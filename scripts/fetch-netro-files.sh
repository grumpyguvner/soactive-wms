#!/bin/bash
#
# Script to retrieve files from Netro ready to import into central database
#

cd /home/activewms/data_transfers/netro_output

rm -f froogle.txt
wget http://sheactive.co.uk/db/froogle.txt

rm -f customers.db.gz
wget ftp://sheactiveDownload:clank1746stat@sheactive.co.uk/customers.db.gz

rm -f customers.db
gunzip customers.db.gz
