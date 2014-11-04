#!/bin/bash
cd /home/activewms/logs
# ADIDAS
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:4ac6f45c-04ed-11e1-80d2-4040632cf0bc" AS categoryid FROM styles s WHERE supplierid = "supp:33f38dbc-799a-7eb7-95cf-552b53cf2dff" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# BARTS
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:b9100d79-a4a2-a890-0116-ef4a1ab08674" AS categoryid FROM styles s WHERE supplierid = "supp:bee3369d-a760-7e3a-fdc1-635d73fc0fed" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# HELLY HANSEN
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:a63f010b-5215-0181-e983-fff020a627fd" AS categoryid FROM styles s WHERE supplierid = "supp:1875c931-6bac-f528-6974-e598ab090030" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# ICEBREAKER
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:12881f07-c59f-2f5a-a661-6f78cef671e7" AS categoryid FROM styles s WHERE supplierid = "supp:0af06b82-8877-da2c-173d-d95edbc82395" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# MERRELL
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:5080834d-3df8-ed89-f6f1-971d60de1931" AS categoryid FROM styles s WHERE supplierid = "supp:95a071d4-969a-7b98-f7e2-38f73768b497" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# NIKE
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:4ad14a6a-04ed-11e1-b1b9-4040632cf0bc" AS categoryid FROM styles s WHERE supplierid = "supp:f5220d2f-cb7b-b78f-b563-4edc698917fc" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# OAKLEY
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:8eb4e51b-606f-7be5-5e00-75e307bdc163" AS categoryid FROM styles s WHERE supplierid = "supp:855d911f-d7a7-90be-4834-35eebb3a2617" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# PANACHE
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:ca73af2e-4262-c010-0281-00434fb7edc7" AS categoryid FROM styles s WHERE supplierid = "supp:660667a2-dbc3-dcb3-6efe-78c065bc598c" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# RONHILL
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:42ea87be-fac5-b413-5a6a-05be8c62ef88" AS categoryid FROM styles s WHERE supplierid = "supp:2a0f6c74-b045-29d8-2e66-dfaaa3ab9951" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# SALOMON
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:fd5c3182-6fbe-35b3-42b0-588422ce5400" AS categoryid FROM styles s WHERE supplierid = "supp:9bc36088-8fb2-0bc5-d3bd-e83a6cfc633b" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# SPEEDO
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:4d359cf0-7611-0025-f798-2693e9980306" AS categoryid FROM styles s WHERE supplierid = "supp:2c05c9f1-5b44-ce65-cb86-cd90c6206056" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# STELLA
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:4adbce7c-04ed-11e1-81ff-4040632cf0bc" AS categoryid FROM styles s WHERE supplierid = "supp:5c9ef373-c2b0-5e17-fec6-dd8e8fb91514" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# THE NORTH FACE
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:6d6884a8-5275-4e97-657d-e3bf1c8dd868" AS categoryid FROM styles s WHERE supplierid = "supp:0d7438d0-997a-2e16-eba8-ea67bb556602" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# ZOCA
mysql -uactivewms -p'St4rl!ght' activewms -e 'INSERT INTO stylestocategories (styleid, categoryid) SELECT s.uuid, "catg:4ae64d20-04ed-11e1-bfc2-4040632cf0bc" AS categoryid FROM styles s WHERE supplierid = "supp:f753479a-320e-9146-1fbe-e8417e6f47ee" AND saleprice > 0 AND webenabled = 1 AND inactive = 0;'
# TRIGGER STYLE UPDATES
mysql -uactivewms -p'St4rl!ght' activewms -e 'UPDATE styles s JOIN stylestocategories sc ON (s.uuid = sc.styleid) SET s.modifiedby = 18 WHERE sc.id > 18053;'
