#!/bin/bash
cd /home/activewms/logs
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms <<ENDOFMYSQL
SELECT stylenumber FROM styles WHERE saleprice > 0 AND webenabled = 1 AND inactive = 0 ORDER BY modifieddate DESC;
ENDOFMYSQL`
# Now run the updates to the website
for STYLE in ${STYLES}
do
  wget "http://shop.sheactive.co.uk/modules/activewms/import_products.php?export=no&housekeeping=no&limit=1&style=$STYLE"
done
# Do Housekeeping
# wget "http://shop.sheactive.co.uk/modules/activewms/housekeeping.php"
# Rebuild search index
wget "https://shop.sheactive.co.uk/backoffice/searchcron.php?token=U0CnRiqn"
