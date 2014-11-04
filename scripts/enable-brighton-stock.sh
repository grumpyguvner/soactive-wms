MYSQL="SELECT s.stylenumber FROM styles s JOIN suppliers b ON (s.supplierid = b.uuid) WHERE b.name IN ('THE NORTH FACE', 'ZOCA', 'NIKE', 'MERRELL', 'SHOCK ABSORBER', 'Adidas by Stella McCartney', 'Icebreaker', 'HELLY HANSEN', 'RONHILL', 'SALOMON', 'Barts', 'ADIDAS', 'Craft', 'Sugoi', 'Sportjock', 'SPEEDO') AND s.inc_brighton = 0 AND s.webenabled = 1 AND s.brighton_stock > 0 ORDER BY s.stylenumber DESC LIMIT 1"
STYLES=`mysql --skip-column-names -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"`
# Now run the updates
for STYLE in ${STYLES}
do
  echo "Enabling stock for $STYLE"

  MYSQL="UPDATE styles SET inc_brighton = 1 WHERE stylenumber = $STYLE;"
  mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"

  MYSQL="UPDATE products p JOIN styles s ON (p.styleid=s.uuid)
         SET p.available_stock=
             (p.bleep_webstore+p.bleep_whse+(p.bleep_brighton*s.inc_brighton))
         WHERE s.stylenumber = $STYLE;"
  mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"

  MYSQL="UPDATE styles
         SET available_stock =
             (SELECT SUM(available_stock) FROM products WHERE products.styleid = styles.uuid)
         WHERE stylenumber = $STYLE;"
  mysql -uactivewms -p'St4rl!ght' activewms -e"$MYSQL"

done
