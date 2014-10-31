#!/bin/bash
#
# Script to adjust stock from Bleep before processing
#

echo "Begin processing ..."

db="bleep_imports"
db_user="activewms"
db_pass="St4rl!ght"

email="datatransfers@sheactive.net"

# Zeroise everything except shoe stock per PRODUCT level for Brighton Stock ...
adjustments[] = "update BRIGHTON, PRODUCTS set BRIGHTON.current_stock=0 WHERE ((BRIGHTON.product_id=PRODUCTS.id) AND NOT (PRODUCTS.department IN (13,27,43,44,45,46,47,48,50,62,63,64,72,77,94,108,110,112,140,141,144,152,165,177,178,179,180,183,185,186,187,188,192,194,197,213,217,218,220,221,222,223,224,227,229,230,242,243,244,245,246,247,248,249,260,279,280,281,287,288,293,301,304,305,306,313,319,320,324,330,332,337,338,342,347,350,351,352,353,355,367,368,369,370,372,374,375,379,380,383,384,385,391,394,396,397)))"
# TEMPORARY ADJUSTMENT: Zeroise CGarden stock at PRODUCT level ...
adjustments[] = "update CGARDEN set current_stock=0"
# TEMPORARY ADJUSTMENT: Reducing all Billingshurst Stock by 1 during stock take ...
# adjustments[] = "update WEBSTORE set current_stock=(current_stock-1) WHERE current_stock>0"
# TEMPORARY ADJUSTMENT: Reducing all Brighton Stock by 1 during stock take ...
# adjustments[] = "update BRIGHTON set current_stock=(current_stock-1) WHERE current_stock>0"
# TEMPORARY ADJUSTMENT: Zeroise phantom stock at PRODUCT level ...
# adjustments[] = "update WHSE set current_stock=0"

# Finally:
# Reset stock level at PRODUCT level to new totals ...
adjustments[] = "update PRODUCTS set current_stock=IFNULL((SELECT current_stock FROM BRIGHTON WHERE BRIGHTON.product_id=PRODUCTS.id)+(SELECT current_stock FROM CGARDEN WHERE CGARDEN.product_id=PRODUCTS.id)+(SELECT current_stock FROM WEBSTORE WHERE WEBSTORE.product_id=PRODUCTS.id)+(SELECT current_stock FROM WHSE WHERE WHSE.product_id=PRODUCTS.id),0)"
# Resetting stock level at STYLE level ...
adjustments[] = "update STYLES set current_stock=IFNULL((SELECT SUM(current_stock) FROM PRODUCTS WHERE PRODUCTS.style=STYLES.id),0)"
# Resetting stock level for ALSTYLES ...
adjustments[] = "update ALSTYLES set current_stock=IFNULL((SELECT SUM(current_stock) FROM PRODUCTS WHERE PRODUCTS.style=ALSTYLES.id),0)"

error_flag=0 # set the flag so that we know if succesfull

for sql in $adjustments
do
  mysql -e $sql -u$db_user -p$db_pass --local $db
  if [ ! $? -eq 0 ] # test the exit status of the "mysql -e" command
  then
    echo "failed to execute sql ( $sql )" | mail -s "Bleep Stock Adjustments *** ERROR ***" $email
    error_flag=1
  fi
done

#
# CHECK THE ERROR FLAG AND EXIT IF NECESSARY
#
if [ $error_flag -eq 1 ]
then
  exit 9 #Exit abnormally
fi

exit 0 #Exit normally