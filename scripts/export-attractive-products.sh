#!/bin/bash
#
# Script to generate and export feeds from database
#

NOWDATE=`date +"20%y-%m-%d"`

cd /var/data_transfers/attractive

# Processing is done from the french side
wget http://attractive.fr/modules/activewms/update_stock.php
mv update_stock.php update_stock_$NOWDATE.log
