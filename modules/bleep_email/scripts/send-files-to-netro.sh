#!/bin/bash
#
# Script to generate CSV files for exporting to Netro
#

echo "Begin processing ..."

db="bleep_imports"
db_user="activewms"
db_pass="St4rl!ght"

email="datatransfers@sheactive.net"

# HOST="sheactive.co.uk"
HOST="testserver.attractive.fr"
USER="sheactiveData"
PASSWD="shedata50"

#sendFiles[] = "ALSTYLES.CSV"
#sendFiles[] = "BRIGHTON.CSV"
#sendFiles[] = "CGARDEN.CSV"
#sendFiles[] = "COLOUR.CSV"
#sendFiles[] = "DEPTS.CSV"
#sendFiles[] = "ECWAREHOUSEDB.CSV"
#sendFiles[] = "GROUPS.CSV"
#sendFiles[] = "PRODUCTS.CSV"
#sendFiles[] = "SIZE.CSV"
#sendFiles[] = "STYLES.CSV"
#sendFiles[] = "supplier.CSV"
#sendFiles[] = "WEBSTORE.CSV"
#sendFiles[] = "WHSE.CSV"

sendFiles=( "ALSTYLES.CSV BRIGHTON.CSV CGARDEN.CSV COLOUR.CSV DEPTS.CSV ECWAREHOUSEDB.CSV GROUPS.CSV PRODUCTS.CSV SIZE.CSV STYLES.CSV supplier.CSV WEBSTORE.CSV WHSE.CSV" )

error_flag=0 # set the flag so that we know if succesfull

for f in $sendFiles
do
  if [ $error_flag -eq 0 ]
  then
    t=${f%\.*}
    echo "SELECT * FROM $t INTO OUTFILE '/tmp/$f' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' -u$db_user -p$db_pass --local $db"

    mysql -e "SELECT * FROM $t INTO OUTFILE '/tmp/$f' FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n'" -u$db_user -p$db_pass --local $db
    if [ ! $? -eq 0 ] # test the exit status of the "mysql -e" command
    then
      echo "failed to generate $f" | mail -s "Netro File Exports *** ERROR ***" $email
      error_flag=1
    fi

#    # Connect to FTP HOST and Send File
#    ftp -n $HOST <<END_SCRIPT
#      quote USER $USER
#      quote PASS $PASSWD
#      dir
#      ascii
#      put $f $f
#      dir
#      quit
#    END_SCRIPT

    if [ ! $? -eq 0 ] # test the exit status of the "ftp" command
    then
      echo "failed to send file $f" | mail -s "Bleep File Imports *** ERROR ***" $email
      error_flag=1
    fi
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
