#!/bin/bash
#
# get list of sports from stylecategories
#
DB="activewms"
U="activewms"
P="St4rl!ght"

MYSQL="SELECT uuid
 FROM stylecategories WHERE parentid=''
 ORDER BY webdisplayname;"
oUuids=`mysql --skip-column-names -u$U -p$P $DB -e"$MYSQL"`
nDisplayOrder=0
for oUuid in ${oUuids}
do
  dNOW=`date +%Y-%m-%d_%H:%M:%S`

  MYSQL="SELECT webdisplayname FROM stylecategories WHERE uuid='$oUuid'"
  nName=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
  echo "$nDisplayOrder $nName"

  # Check if we already have a sport created
  MYSQL="SELECT IFNULL(uuid,'') FROM sports WHERE name='$nName'"
  nUuid=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
  if [ "$nUuid" == "" ]; then
    nUuid=`uuid`
    nUuid=`echo "sprt:$nUuid"`

    nParentId=""
    nDisplayOrder=`expr $nDisplayOrder + 1`
    nWebDescription=""
    nWebEnabled=1
    nWebDisplayName=${nName}
    tmp=${nName//&/and}
    tmp=${tmp//[^A-Za-z0-9]/-}
    tmp=$(echo $tmp | tr "[:upper:]" "[:lower:]")
    tmp=${tmp//--/-}
    nWebUrl=${tmp//--/-}

    MYSQL="INSERT INTO sports
           (uuid,
            name,
            parentid,
            displayorder,
            webdescription,
            webenabled,
            webdisplayname,
            weburl,
            createdby,
            modifiedby,
            creationdate)
           VALUES
           ('$nUuid',
            '$nName',
            '$nParentId',
            $nDisplayOrder,
            '$nWebDescription',
            $nWebEnabled,
            '$nWebDisplayName',
            '$nWebUrl',
            2,2,'$dNow');"
    RESULT=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
  fi

  MYSQL="INSERT IGNORE INTO stylestosports (styleid,sportid)
          SELECT styleid, '$nUuid' FROM stylestostylecategories WHERE stylecategoryid='$oUuid';"
  RESULT=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")

  #
  # Now creat categories for children of sports
  #
  MYSQL="SELECT uuid
   FROM stylecategories WHERE parentid='$oUuid'
   ORDER BY webdisplayname;"
  scUuids=`mysql --skip-column-names -u$U -p$P $DB -e"$MYSQL"`
  cDisplayOrder=0
  for scUuid in ${scUuids}
  do
    dNOW=`date +%Y-%m-%d_%H:%M:%S`

    MYSQL="SELECT SUBSTRING(webdisplayname,4) FROM stylecategories WHERE uuid='$scUuid'"
    cName=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
    echo "$cDisplayOrder $cName"
   
    # Check if we already have a category created
    MYSQL="SELECT IFNULL(uuid,'') FROM categories WHERE name='$cName'"
    cUuid=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
    if [ "$cUuid" == "" ]; then
      cUuid=`uuid`
      cUuid=`echo "catg:$cUuid"`

      cParentId=""
      cDisplayOrder=`expr $cDisplayOrder + 1`
      cWebDescription=""
      cWebEnabled=1
      cWebDisplayName=${cName}
      tmp=${cName//&/and}
      tmp=${tmp//[^A-Za-z0-9]/-}
      tmp=$(echo $tmp | tr "[:upper:]" "[:lower:]")
      tmp=${tmp//--/-}
      cWebUrl=${tmp//--/-}

      MYSQL="INSERT INTO categories
             (uuid,
              name,
              parentid,
              displayorder,
              webdescription,
              webenabled,
              webdisplayname,
              weburl,
              createdby,
              modifiedby,
              creationdate)
             VALUES
             ('$cUuid',
              '$cName',
              '$cParentId',
              $cDisplayOrder,
              '$cWebDescription',
              $cWebEnabled,
              '$cWebDisplayName',
              '$cWebUrl',
              2,2,'$dNow');"
      RESULT=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
    fi

    MYSQL="INSERT IGNORE INTO stylestocategories (styleid,categoryid)
            SELECT styleid, '$cUuid' FROM stylestostylecategories WHERE stylecategoryid='$scUuid';"
    RESULT=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")

    # And insert this style into the parent sport too
    MYSQL="INSERT IGNORE INTO stylestosports (styleid,sportid)
            SELECT styleid, '$nUuid' FROM stylestostylecategories WHERE stylecategoryid='$scUuid';"
    RESULT=$(mysql -u$U -p$P $DB -sN -e "$MYSQL")
  done
done
