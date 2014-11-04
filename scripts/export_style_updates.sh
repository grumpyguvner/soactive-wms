#!/bin/bash
# ENSURE ONLY ONE COPY OF SCRIPT RUNS
# YOU MUST cd TO scripts DIRECTORY BEFORE RUNNING`
source ./pid.sh

cd /home/activewms/logs


# Find which styles have been updated since the last execution
MYSQL="SELECT value FROM settings WHERE name='last_update_check';"
echo "$MYSQL"
LAST_UPDATE=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
MYSQL="SELECT stylenumber FROM styles WHERE modifieddate > \"$LAST_UPDATE\" ORDER BY modifieddate;"
echo "$MYSQL"
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`

# Now run the updates to the website
for STYLE in ${STYLES}
do
#  wget "http://www.sheactive.co.uk/modules/activewms/import_products.php?export=no&housekeeping=no&limit=1&style=$STYLE"
#  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE update pushed to www.sheactive.co.uk\");"
#  wget "http://www.attractive.fr/modules/activewms/import_products.php?export=no&housekeeping=no&limit=1&style=$STYLE"
#  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE update pushed to www.attractive.fr\");"
  wget "http://zeus.soactive.com/admin/cron.php?route=tool/wms/importStock&stylenumber=$STYLE"
  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH STOCK UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE stock update pushed to www.soactive.com\");"
  wget "http://www.sportsbrabar.com/modules/activewms/import_products.php?export=no&housekeeping=no&limit=1&style=$STYLE"
  mysql -uactivewms -p'St4rl!ght' activewms -e "INSERT INTO log (type,userid,ip,value) VALUES (\"PUSH UPDATE\",\"usr:42e0cc76-3c31-d9b6-ff12-fe4adfd15e75\",\"localhost\",\"$STYLE update pushed to www.sportsbrabar.com\");"
  # Slow the updates down by sleeping for a few seconds. Trying to
  # stop prestashop from applying site-wide discount bug, this might not
  # be needed once mulitple sites are being updated...
  # sleep 1
  mysql -uactivewms -p'St4rl!ght' activewms -e "UPDATE settings JOIN styles SET settings.value = styles.modifieddate WHERE styles.stylenumber = \"$STYLE\" AND settings.name=\"last_update_check\";"
done
