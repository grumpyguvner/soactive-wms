#!/bin/bash
#
# Script to import files from Bleep into database ready for processing
#
# This module assumes that the files have been copied to the local folder
#  /modules/bleep_email/attachments - contains the files exported from Bleep server (SHESERVER)
#

echo "Begin processing ..."

db="bleep_imports"
db_user="activewms"
db_pass="St4rl!ght"

email="datatransfers@sheactive.net"

cd ../attachments

error_flag=0 # set the flag so that we know if succesfull
FILES="*"

for f1 in $FILES
do
  echo "Processing $f1 ..."

  #
  # IMPORT FILE TO DATABASE
  #

  t=${f1%\.*}
  echo "Remove existing records from $t ..."

  mysql -e "DELETE FROM $t" -u$db_user -p$db_pass --local $db
  if [ ! $? -eq 0 ] # test the exit status of the "mysql -e" command
  then
    echo "failed to delete existing records for $t" | mail -s "Bleep File Imports *** ERROR ***" $email
    error_flag=1
  else

    echo "Begining import for $f1 ..."

    mysqlimport -u$db_user -p$db_pass --local --replace --verbose --fields-terminated-by="," --lines-terminated-by="\n" $db $f1 > /dev/null
    if [ ! $? -eq 0 ] # test the exit status of the "mysqlimport" command
    then
      echo "failed to import $f1" | mail -s "Bleep File Imports *** ERROR ***" $email
      error_flag=1
    else

      rm -f $f1 #remove the file
      if [ $? -ne 0 ] # test the status of the "rm" command
      then
        echo "failed to remove $f1" | mail -s "Bleep File Imports *** ERROR ***" $email
        error_flag=1
      fi

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