#!/bin/bash
MYSQL="SELECT stylenumber
 FROM styles WHERE season='2011 Q4' OR season='2011 Q3'
 ORDER BY stylenumber;"
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
# Now run check the images exist
for STYLE in ${STYLES}
do
  MYSQL="SELECT CONCAT(image_folder,main_image)
   FROM styles WHERE stylenumber='$STYLE';"
  FILES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
  for FILE in ${FILES}
  do
    if [ ! -f /home/activewms/public_html$FILE ]
      then
        # echo "$STYLE - $FILE file exists"
      #else
        echo "$STYLE - $FILE NOT FOUND"
    fi
  done
done
