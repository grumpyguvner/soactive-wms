#!/bin/bash
#
# Script to create attractive data
#

echo "Begin processing ..."

mysql -u activewms -p'St4rl!ght' -e 'INSERT INTO styles_translations 
			SELECT	NULL AS id,
				CONCAT("tran:",UUID()) AS uuid,
				styles.uuid AS styleid,
				"www.attractive.fr" AS site,
				IFNULL(styles.stylename,"") AS stylename,
				IFNULL(styles.description,"") AS description,
				IFNULL(styles.keywords,"") AS keywords,
				"" AS webdescription,
				1 AS createdby,
				NOW() AS creationdate,
				1 AS modfiedby,
				NOW() AS modifieddate,
				0 AS inactive,
				0 AS ecotax,
				ROUND((styles.unitprice*1.35),0) AS price,
				0 AS reduction_price,
				0 AS reduction_percent,
				"" AS link_rewrite,
				"" AS meta_description,
				"" AS meta_title,
				"" AS available_now,
				"" AS available_later,
				ROUND((styles.unitcost*1.35),2) AS wholesale_price,
				0 AS on_sale
			   FROM styles
		      LEFT JOIN suppliers ON (styles.supplierid = suppliers.uuid)
		      LEFT JOIN styles_translations ON (styles.uuid = styles_translations.styleid AND styles_translations.site = "www.attractive.fr")
			  WHERE styles_translations.uuid IS NULL
		       ORDER BY season DESC, stylenumber DESC;' activewms

mysql -u activewms -p'St4rl!ght' -e 'UPDATE styles LEFT JOIN styles_translations ON (styles.uuid = styles_translations.styleid AND styles_translations.site = "www.attractive.fr")  SET styles.modifieddate = NOW() WHERE styles_translations.modifieddate > styles.modifieddate;' activewms

exit 0 #Exit normally
