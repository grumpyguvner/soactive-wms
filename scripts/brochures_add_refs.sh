#!/bin/bash

# Find which records which have no reference number
MYSQL="SELECT MAX(SUBSTR(reference,2)) FROM brochure_requests;"
LAST_REF=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`

MYSQL="SELECT id FROM brochure_requests WHERE NOT reference LIKE 'L%' AND inactive = 0 ORDER BY id;"
RECORDS=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`

# Now run the updates to the website
i=0;
for RECORD in ${RECORDS}
do
  i=`expr $i + 1`
  LAST_REF=`expr $LAST_REF + 1`
  NEW_REF=`printf "L%07d" "$LAST_REF"`
  echo "Record No. $i: $RECORD reference $NEW_REF"

  MYSQL="UPDATE brochure_requests SET reference = '$NEW_REF'
         WHERE id=$RECORD;"
  UPDATE=`mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
done

echo "Records processed $i"
