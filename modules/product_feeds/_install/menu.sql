INSERT INTO `menu` (`uuid`, 
                    `name`,
                    `link`,
                    `parentid`,
                    `displayorder`,
                    `createdby`,
                    `modifiedby`,
                    `creationdate`,
                    `modifieddate`,
                    `roleid`)
            VALUES ('menu:fc2f4f40-8897-11e1-8b7e-001d0923519e',
                    'Product Feeds',
                    '',
                    NULL,
                    2,
                    1,
                    1,
                    NOW(),
                    NOW(),
                    'role:dbc4ff20-888d-11e1-aa23-001d0923519e'
                    );
INSERT INTO `menu` (`uuid`,
                    `name`,
                    `link`,
                    `parentid`,
                    `displayorder`,
                    `createdby`,
                    `modifiedby`,
                    `creationdate`,
                    `modifieddate`,
                    `roleid`)
            VALUES ('menu:0aa806d4-8898-11e1-a6a9-001d0923519e',
                    'Upload Now',
                    'modules/product_feeds/feed_uploader.php',
                    'menu:fc2f4f40-8897-11e1-8b7e-001d0923519e',
                    2,
                    1,
                    1,
                    NOW(),
                    NOW(),
                    'role:dbc4ff20-888d-11e1-aa23-001d0923519e'
                    );
