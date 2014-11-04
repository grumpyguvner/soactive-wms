#!/bin/bash
cd /home/activewms/logs

# This script peforms an update for all webenabled active styles
#
# DO NOT use a cron job to run these tasks every minute because when
# there are a lot of updates then you get multiple tasks running and
# overlapping.

MYSQL="SELECT stylenumber FROM styles WHERE webenabled = 1 AND inactive = 0 AND modifieddate > '2014-10-29 22:00:00' ORDER BY LPAD(stylenumber,6,'0') DESC;"
echo "$MYSQL"
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`

# Now run the updates to the website
for STYLE in ${STYLES}
do
  wget "http://www.soactive.com/admin/cron.php?route=tool/wms/import&stylenumber=$STYLE"
  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE update pushed to www.soactive.com\");"
  wget "http://www.sportsbrabar.com/modules/activewms/import_products.php?export=no&housekeeping=no&limit=1&style=$STYLE"
  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE update pushed to www.sportsbrabar.com\");"
done

