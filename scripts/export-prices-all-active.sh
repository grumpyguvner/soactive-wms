#!/bin/bash
cd /home/activewms/logs

# This script peforms an update for all webenabled active styles
#
# DO NOT use a cron job to run these tasks every minute because when
# there are a lot of updates then you get multiple tasks running and
# overlapping.

MYSQL="SELECT stylenumber FROM styles WHERE webenabled = 1 AND inactive = 0 ORDER BY LPAD(stylenumber,6,'0') DESC;"
echo "$MYSQL"
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`

# Now run the updates to the website
for STYLE in ${STYLES}
do
  wget "http://triton.soactive.com/admin/cron.php?route=tool/wms/importStock&stylenumber=$STYLE"
done
