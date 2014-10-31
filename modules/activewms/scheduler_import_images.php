<?php

set_time_limit(0);

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

include("include/tables.php");

class importImageFiles{

	function importImageFiles($db){
		$this->db = $db;
	}//end method --cleanImports--

	//This method scans all files in the named folder
	//and attempts to import the images
        // default folder is /home/activewms/public_html/uploads/
	function beginImport($dir="/home/activewms/public_html/uploads/"){

            $this->importMainImages($dir);

            return true;

                // Open a known directory, and proceed to read its contents
                if (is_dir($dir)) {
                    if ($dh = opendir($dir)) {
                        while (($file = readdir($dh)) !== false) {
                            if ($file !== '.' && $file !== '..') {
                                $this->importFile($file);
                            }
                        }
                        closedir($dh);
                    }
                }

	}//end method --beginImport-

	//This method reads through the styles table and imports files
        //associated with the main_image field
	function importMainImages($dir){

//		$querystatement = "
//			SELECT
//                                s.stylename AS name,
//                                s.uuid AS styleid,
//                                c.uuid AS colourid,
//                                s.stylenumber,
//                                c.bleepid AS colournumber,
//                                1 AS webenabled,
//                                s.stylename AS alt_text,
//                                IFNULL(s.image_folder,'$dir') AS image_folder,
//                                IFNULL(s.main_image,'') AS main_image,
//                                IFNULL(s.alt_image1,'') AS alt_image1,
//                                IFNULL(s.alt_image2,'') AS alt_image2,
//                                IFNULL(s.alt_image3,'') AS alt_image3,
//                                IFNULL(s.alt_image4,'') AS alt_image4
//                        FROM styles s
//                        LEFT JOIN products p ON (s.uuid = p.styleid)
//                             JOIN colours c ON (p.colourid = c.uuid)
//                        WHERE (s.main_image <> ''
//                           OR s.alt_image1 <> ''
//                           OR s.alt_image2 <> ''
//                           OR s.alt_image3 <> ''
//                           OR s.alt_image4 <> '')
//                          AND s.image_folder <> ''
//                        GROUP BY s.uuid, p.colourid
//                        ;";

		$querystatement = "
			SELECT
                                s.stylename AS name,
                                s.uuid AS styleid,
                                c.uuid AS colourid,
                                s.stylenumber AS stylenumber,
                                c.bleepid AS colournumber,
                                1 AS webenabled,
                                s.stylename AS alt_text,
                                '/images/items/historic/' AS image_folder,
                                CONCAT('IMG',LPAD(s.stylenumber,4,'0'),LPAD(c.bleepid,4,'0'),'.jpg') AS main_image,
                                IFNULL(s.alt_image1,'') AS alt_image1,
                                IFNULL(s.alt_image2,'') AS alt_image2,
                                IFNULL(s.alt_image3,'') AS alt_image3,
                                IFNULL(s.alt_image4,'') AS alt_image4
                        FROM styles s
                        LEFT JOIN products p ON (s.uuid = p.styleid)
                             JOIN colours c ON (p.colourid = c.uuid)
                        WHERE s.stylenumber < 9999
                        GROUP BY s.uuid, p.colourid
                        ORDER BY s.stylenumber;";

		$importquery = $this->db->query($querystatement);

		if($this->db->numRows($importquery)){

			while($therecord = $this->db->fetchArray($importquery)){

                            if(!$this->imagesExist($therecord["styleid"],$therecord["colourid"])){

                                echo $therecord["stylenumber"].":".$therecord["colournumber"]." ".$therecord["name"]."<br/>"."<br/>";

                                $sortOrder = 0;
                                $success = true;
                                $success = $success && $this->importFile($therecord,0,$therecord["main_image"]);
                                $success = $success && $this->importFile($therecord,1,$therecord["alt_image1"]);
                                $success = $success && $this->importFile($therecord,2,$therecord["alt_image2"]);
                                $success = $success && $this->importFile($therecord,3,$therecord["alt_image3"]);
                                $success = $success && $this->importFile($therecord,4,$therecord["alt_image4"]);

                                if($success){
                                    //If all files imported successfully then remove the references in the styles table
                                    echo "Updating style ".$therecord["stylenumber"]." ".$therecord["name"]."<br/>"."<br/>";
                                    $querystatement = "
                                            UPDATE styles SET
                                                    image_folder = '',
                                                    main_image = '',
                                                    alt_image1 = '',
                                                    alt_image2 = '',
                                                    alt_image3 = '',
                                                    alt_image4 = ''
                                            WHERE
                                                    uuid = '".$therecord["styleid"]."'
                                            ;";

                                    $queryresult = $this->db->query($querystatement);
                                }
                                
                            }else{
                                echo "Images already exist for ".$therecord["stylenumber"].":".$therecord["colournumber"]." ".$therecord["name"]."<br/>"."<br/>";
                            }

                            //Ensure that other processes continue to run!!
                            usleep(10000);
                        }

		}//end if

	}//end method --importMainImages-

	//This method returns the number of image records that exist
        //for the specified style/colour combo
	function imagesExist($styleid,$colourid){

		$querystatement = "
			SELECT
                                uuid
                        FROM styles_images
                        WHERE
                                (styleid = '$styleid'
                            AND colourid = '$colourid')
                        ;";

		$queryresult = $this->db->query($querystatement);

		return $this->db->numRows($queryresult);

	}//end method --imagesExist-

	//This method should import all images from the uploads folder
	//if there is a matching record in the styles table
	function importFile($variables,$sortorder,$file){

            if($file=="")
                return true;
            if(!strpos($file, ".")){
                echo "Filename [".$file."] is invalid.<br/>";
                return false;
            }

            list($name,$extension) = explode(".",$file);
            $filepath = "/home/activewms/public_html".$variables["image_folder"].$file;
            if(!file_exists($filepath)){
                echo "File [".$filepath."] does not exist.<br/>";
                return false;
            }

            $variables["displayorder"] = $sortorder;
            $variables["image_name"] = $file;
            switch($extension){
                case "jpg":
                    $variables["image_type"] = "image/jpeg";
                    break;
                case "png":
                    $variables["image_type"] = "image/png";
                    break;
                default:
                    echo "File type [".$file."] is not supported.<br/>";
                    return false;
            }

            echo "importing: $filepath<br/>";

            $variables["uuid"] = uuid("simg:");
            $variables["image"] = $this->getPicture($filepath);

            $imageTable = new phpbmsTable($this->db, "tbld:76d711ca-0412-11e1-a021-0017083b723b");
            $variables = $imageTable->prepareVariables($variables);
            $errorArray = $imageTable->verifyVariables($variables);

            if(!count($errorArray)){

                if($imageTable->insertRecord($variables)){
                    if(is_writable($filepath)){
                        unlink($filepath);
                        return true;
                    }else{
                        echo "Unable to delete [".$filepath."] please check permissions.<br/>";
                        return false;
                    }
                }

            } else {

                foreach($errorArray as $error)
                    $logError = new appError(-900, $error, "Verification Error");

            }//end if

            return false;

	}//end method --importFile-

        function getPicture($filepath){

                if (function_exists('file_get_contents')) {
//                        $file = addslashes(file_get_contents($filepath));
                        $file = file_get_contents($filepath);
                } else {
                        // If using PHP < 4.3.0 use the following:
//                        $file = addslashes(fread(fopen($filepath, 'r'), filesize($filepath)));
                        $file = fread(fopen($filepath, 'r'), filesize($filepath));
                }

                return $file;
        }

}//end class --cleanImports--

if(!isset($noOutput) && isset($db)){

    $clean = new importImageFiles($db);
    $clean->beginImport();

}//end if
?>
