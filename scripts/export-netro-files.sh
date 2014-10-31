#!/bin/bash
#
# Script to export files to Netro
#
# This module assumes that the files have been copied to the local folder
#  /home/activewms/data_transfers/netro_input - contains the files exported from the database
#
# This script ensures that all 12 files it expects are in the above folder, if not
#  it does not continue, this is so that we do not begin an upload before the
#  previous process has completed.
#
# If all files (see below) are present then it uploads them to the ftp server using
#  wput and removes them if the transfer was successful (a feature of wput).
#
# The complete list of files that this script looks for is:
#  ALSTYLES.CSV
#  BRIGHTON.CSV
#  CGARDEN.CSV
#  COLOUR.CSV
#  DEPTS.CSV
#  GROUPS.CSV
#  PRODUCTS.CSV
#  SIZE.CSV
#  STYLES.CSV
#  supplier.CSV
#  WEBSTORE.CSV
#  WHSE.CSV
#

echo "Begin processing ..."

# make sure that remote folder is not mounted
# no need to check output since we expect an error
umount /mnt/sheactive.co.uk/db

cd /home/activewms/data_transfers/netro_input

# move files output from mysql to working folder
mv /tmp/ALSTYLES.CSV .
mv /tmp/BRIGHTON.CSV .
mv /tmp/CGARDEN.CSV .
mv /tmp/COLOUR.CSV .
mv /tmp/DEPTS.CSV .
mv /tmp/GROUPS.CSV .
mv /tmp/PRODUCTS.CSV .
mv /tmp/SIZE.CSV .
mv /tmp/STYLES.CSV .
mv /tmp/supplier.CSV .
mv /tmp/WEBSTORE.CSV .
mv /tmp/WHSE.CSV .

#
# FIRST WE CHECK THAT ALL 12 FILES ARE PRESENT
#

if [ ! -r "ALSTYLES.CSV" ] ;  then
  echo "ALSTYLES.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "BRIGHTON.CSV" ] ;  then
  echo "BRIGHTON.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "CGARDEN.CSV" ] ;  then
  echo "CGARDEN.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "COLOUR.CSV" ] ;  then
  echo "COLOUR.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "DEPTS.CSV" ] ;  then
  echo "DEPTS.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "GROUPS.CSV" ] ;  then
  echo "GROUPS.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "PRODUCTS.CSV" ] ;  then
  echo "PRODUCTS.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "SIZE.CSV" ] ;  then
  echo "SIZE.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "STYLES.CSV" ] ;  then
  echo "STYLES.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "supplier.CSV" ] ;  then
  echo "supplier.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "WEBSTORE.CSV" ] ;  then
  echo "WEBSTORE.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi
if [ ! -r "WHSE.CSV" ] ;  then
  echo "WHSE.CSV not present so exiting ..."
  exit 0 #No need to throw an error
fi

echo "All required files are present so continuing ..."

# make sure that remote folder is mounted
mount /mnt/sheactive.co.uk/db
if [ $? -ne 0 ] # test the status of the "mount" command
then
  echo "failed to mount the remote folder" | mail -s "Website Database Update *** ERROR ***" datatransfers@sheactive.net
  exit 99 #exit with error
fi

rm -f /home/activewms/data_transfers/netro_working/* #remove all previous working files

error_flag=0 # set the flag so that we know if succesfull
FILES="*"

for f1 in $FILES
do

  if [ $error_flag -eq 0 ] #Only process files whilst no error exists
  then

    echo "Processing $f1 ..."

    f2="/home/activewms/data_transfers/netro_working/$f1" #keep a copy of the current file incase we have to reset
    cp $f1 $f2
    if [ $? -ne 0 ] # test the status of the "cp" command
    then
      echo "failed to make a backup copy of $f1" | mail -s "Website Database Update *** ERROR ***" datatransfers@sheactive.net
      error_flag=1
    fi

    #
    # Try the export 3 times before failing
    #
    try=1
    success=0
    while [ $try -le 3 ]
    do
      #
      # EXPORTING FILE TO NETRO SERVER
      #
      echo "comparing local and remote copies ... "
      cmp $f1 /mnt/sheactive.co.uk/db/$f1 > /dev/null
      if [ $? -eq 0 ] # test the status of the "cmp" command
      then
        echo "local and remote copies are the same."
        success=1
        try=3
      else
        echo "uploading the file (attempt $try) ..."
#      	wput --reupload $f1 ftp://sheactiveData:shedata50@sheactive.co.uk/
        cp -f $f1 /mnt/sheactive.co.uk/db/$f1
      fi

      try=`expr $try + 1`

    done

    if [ $success -ne 1 ] # test if transfer successful
    then
      echo "failed to transfer $f1" | mail -s "Website Database Update *** ERROR ***" datatransfers@sheactive.net
      error_flag=1
    else
      rm $f1 #remove file when successful
    fi

  fi

done

# unmount the remote folder
umount /mnt/sheactive.co.uk/db

#
# CHECK THE ERROR FLAG AND RESET FILES IF NECESSARY
#
if [ $error_flag -eq 1 ]
then
  mv /home/activewms/data_transfers/netro_working/*.CSV /home/activewms/data_transfers/netro_input #move any processed files back into the export directory
  exit 9 #Exit abnormally
fi

#
# NOW LETS RUN THE NETRO SIDE OF THINGS
#

  
cd /home/activewms/data_transfers/netro_working

#
# 2011-02-08 Disabled auto call for Netro files due
#            to second part being called 6 times twice
#            in one week.
#
# echo "Executing NETRO update part 1 ..."
# wget http://sheactive.co.uk/db/db_mover_new.php
# if [ $? -eq 0 ] # test the status of the "wget" command
# then
#   echo "Executing NETRO update part 2 ..."
#   wget http://sheactive.co.uk/db/db_mover_new.tpl

#   if [ $? -ne 0 ] # test the status of the "wget" command
#   then
#     echo "there was an error with the second part of the update ..." | mail -s "Website Database Update *** ERROR ***" datatransfers@sheactive.net
#     error_flag=1
#   fi
# else
#   echo "there was an error with the first part of the update ..." | mail -s "Website Database Update *** ERROR ***" datatransfers@sheactive.net
#   error_flag=1
# fi
#
# 2011-02-08 end modifications
#

#
# CHECK THE ERROR FLAG AND RESET FILES IF NECESSARY
#
if [ $error_flag -eq 1 ]
then
  mv /home/activewms/data_transfers/netro_working/*.CSV /home/activewms/data_transfers/netro_input #move any processed files back into the export directory
  exit 9 #Exit abnormally
fi

#
# AND FINALLY, EMAIL THE OUTPUT TO ALEX 
#
echo "All completed ... sending completion email"
EMAILSUBJECT="Website Database Update"
EMAILMESSAGE="/tmp/`date +%s`-message"
#EMAILMESSAGE=`mktemp`
#
# 2011-02-08 Just ask Alex to run update instead.
#
# echo "Website Database Update at `date`" > ${EMAILMESSAGE}
# echo " " >> ${EMAILMESSAGE}
# sed -e :a -e 's/<[^>]*>//g;/</N;//ba' db_mover_new.tpl >> ${EMAILMESSAGE}
echo "Website Database Update at `date`" > ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "Please use the link below to complete the update:" >> ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "http://sheactive.co.uk/db/db_mover_new.php" >> ${EMAILMESSAGE}
#
# 2011-02-08 end modifications
#
echo " " >> ${EMAILMESSAGE}
echo " " >> ${EMAILMESSAGE}
echo "--- End of Report ---" >> ${EMAILMESSAGE}
mail -s "${EMAILSUBJECT}" "datatransfers@sheactive.net" < ${EMAILMESSAGE}
/bin/rm ${EMAILMESSAGE}

exit 0 #Exit normally