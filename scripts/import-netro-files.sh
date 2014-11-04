#!/bin/bash
#
# Script to import files from Netro into central database
#
# This module assumes that the files have been copied to the local folder
#  /home/activewms/netro_output - contains the files exported from the website
#  there is a cron job which does this once per house
#
# This script compares each file in the above folders to the last imported file
#  stored in /home/activewms/database_imports
#  if ANY difference is found then it is assumed that an import is required
# 
#

cd /home/activewms/data_transfers/netro_output

FILES="froogle.txt customers.db SheactiveSalesRecords.csv"

for f1 in $FILES
do
  echo "Processing $f1 ..."

  f2="/home/activewms/data_transfers/netro_working/$f1"
  if [ ! -r "$f2" ]
  then

   echo "$f2 does not exist so importing ..."
   import_flag=1
  else

   echo "Comparing to $f2 ..."
   cmp $f1 $f2 > /dev/null
   if [ $? -eq 0 ] # test the exit status of the "cmp" command
   then
     echo "files are identical so not proceeding."
     import_flag=0
   else
     echo "files differ so importing ..."
     import_flag=1
   fi
  fi

  if [ $import_flag -eq 1 ]
  then
    echo "Begining import for $f1 ..."

    if [ "$f1" = "SheactiveSalesRecords.csv" ] # different line terminaters!
    then
	mysqlimport -u activewms -p'St4rl!ght' --local --ignore-lines=1 --verbose --fields-optionally-enclosed-by='"' --fields-terminated-by="\t" --lines-terminated-by="\n"  activewms $f1 > /dev/null
    fi

    if [ ! $? -eq 0 ] # test the exit status of the "mysqlimport" command
    then
      echo "there was a problem importing to the database ..."
      exit 99 #Exit abnormally
    fi

    if [ "$f1" = "customers.db" ] # different line terminaters!
    then
	mysqlimport -u activewms -p'St4rl!ght' --local --delete --verbose --fields-terminated-by="\t" --lines-terminated-by="\r"  activewms $f1 > /dev/null
    fi

    if [ ! $? -eq 0 ] # test the exit status of the "mysqlimport" command
    then
      echo "there was a problem importing to the database ..."
      exit 99 #Exit abnormally
    fi

    if [ "$f1" = "froogle.txt" ] # different line terminaters!
    then
	mysqlimport -u activewms -p'St4rl!ght' --local --delete --ignore-lines=1 --verbose --fields-terminated-by="\t" --lines-terminated-by="\n"  activewms $f1 > /dev/null
    fi
    mysql -u activewms -p'St4rl!ght' -e 'UPDATE froogle f JOIN styles s ON (f.id = s.stylenumber) SET s.webdescription = f.description;' activewms > /dev/null
    mysql -u activewms -p'St4rl!ght' -e 'UPDATE styles join departments on (departments.categoryid = styles.categoryid) join suppliers on (suppliers.uuid = styles.supplierid) set styles.description = concat((case departments.bleepid when 53 then "" when 54 then "" when 80 then "" when 102 then "" when 270 then "" when 277 then "" when 356 then "" else "Womens " end), (case trim(suppliers.name) when "adidas Pink Ribbon Range" then "Adidas" when "adidas by Stella McCartney" then "Adidas" when "The North Face" then "North Face" else trim(suppliers.name) end), " ", trim(styles.stylename));' activewms > /dev/null

    if [ ! $? -eq 0 ] # test the exit status of the "mysqlimport" command
    then
      echo "there was a problem importing to the database ..."
      exit 99 #Exit abnormally
    fi
  fi

    # TODO: MAKE A BACKUP OF THE FILE SO THAT WE HAVE AN AUDIT TRAIL
   cp -p $f1 $f2

done

exit 0 #Exit normally
