PARENT_POS=1
MYSQL="SELECT c.uuid
 FROM categories c
 WHERE c.parentid = ''
 ORDER BY c.name;"
CATEGORIES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
# Now run the updates
for CATEGORY in ${CATEGORIES}
do
  echo "Resequencing category $CATEGORY"

  MYSQL="UPDATE categories
        SET displayorder = $PARENT_POS
   WHERE uuid = '$CATEGORY;'"
  mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"
  PARENT_POS=`expr $PARENT_POS + 1`

  MYSQL="SELECT c.uuid
   FROM categories c
   WHERE c.parentid = '$CATEGORY'
   ORDER BY c.name;"
  CHILDREN=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
  CHILD_POS=1
  for CHILD in ${CHILDREN}
  do
    echo "Resequencing category $CATEGORY child $CHILD position $CHILD_POS"

    MYSQL="UPDATE categories
          SET displayorder = $CHILD_POS
     WHERE uuid = '$CHILD';"
    mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"
    CHILD_POS=`expr $CHILD_POS + 1`
  done
done
