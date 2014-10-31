<?php

set_time_limit(0);

include_once("../include/session.php");
include("include/tables.php");

class importImageFiles{

	function importImageFiles($db){
		$this->db = $db;
	}//end method --cleanImports--

	//This method scans all files in the named folder
	//and attempts to import the images
        // default folder is /home/www-data/soactive-wms/uploads/
	function beginImport($dir="/home/www-data/soactive-wms/uploadify/uploads/"){

            echo "<ul>";
            // Open a known directory, and proceed to read its contents
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file !== '.' && $file !== '..') {
                            echo "<li>Processing ".$file." size ".filesize($file);
                            if(!$this->importMainImages($dir,$file))
                                if(!$this->importMainImages($dir,$file,'alt_image1',1))
                                    if(!$this->importMainImages($dir,$file,'alt_image2',2))
                                        if(!$this->importMainImages($dir,$file,'alt_image3',3))
                                            if(!$this->importMainImages($dir,$file,'alt_image4',4))
                                                echo "<br/>No reference to this image found in Styles (or, *** CHECK THAT THE PRODUCTS HAVE BEEN IMPORTED! ***)";

                            $filepath = $dir.$file;
                            if(is_writable($filepath)){
                                unlink($filepath);
                            }else{
                                echo "<br/>Unable to delete [".$filepath."] please check permissions.";
                            }
                            echo "</li>";
                        }
                    }
                    closedir($dh);
                }
            }
            echo "</ul>";

	}//end method --beginImport-

	//This method reads through the styles table and finds files
        //associated with the image fields
	function importMainImages($dir,$file,$imagefield='main_image',$sortorder=0){

		$querystatement = "
			SELECT
                                s.stylename AS name,
                                s.uuid AS styleid,
                                c.uuid AS colourid,
                                s.stylenumber,
                                c.bleepid AS colournumber,
                                1 AS webenabled,
                                s.stylename AS alt_text
                        FROM styles s
                        LEFT JOIN products p ON (s.uuid = p.styleid)
                             JOIN colours c ON (p.colourid = c.uuid)
                        WHERE (s.".$imagefield." LIKE '".$file."')
                        GROUP BY s.uuid, p.colourid
                        ;";

		$importquery = $this->db->query($querystatement);

		if($this->db->numRows($importquery)){

			while($therecord = $this->db->fetchArray($importquery)){

                            echo "<br/>".$therecord["stylenumber"].":".$therecord["colournumber"]." ".$therecord["name"];

                            $success = true;
                            $success = $success && $this->importFile($therecord,$sortorder,$dir,$file);

                            if($success){
                                //If all files imported successfully then remove the references in the styles table
                                echo "<br/>Removing reference to file from style ".$therecord["stylenumber"]." ".$therecord["name"];
                                $querystatement = "
                                        UPDATE styles SET
                                                ".$imagefield." = ''
                                        WHERE
                                                uuid = '".$therecord["styleid"]."'
                                        ;";

                                $queryresult = $this->db->query($querystatement);
                            }

                            //Ensure that other processes continue to run!!
                            usleep(10000);
                            return $success;
                        }

		}//end if

	}//end method --importMainImages-

	//This method returns the uuid of existing image record
        //for the specified style/colour combo.
	function imageExists($styleid,$colourid,$sortorder){

		$querystatement = "
			SELECT
                                uuid
                        FROM styles_images
                        WHERE
                                (styleid = '$styleid'
                            AND colourid = '$colourid'
                            AND displayorder = $sortorder)
                        ;";

		$queryresult = $this->db->query($querystatement);

		if ($this->db->numRows($queryresult)){
                    $therecord = $this->db->fetchArray($queryresult);
                    return $therecord["uuid"];
                } else {
                    return false;
                }

	}//end method --imageExists-

	//This method should import all images from the uploads folder
	//if there is a matching record in the styles table
	function importFile($variables,$sortorder,$dir,$file){

            if($file==""){
                echo "<br/>Filename is empty.";
                return true;
            }
            if(!strpos($file, ".")){
                echo "<br/>Filename [".$file."] is invalid.";
                return false;
            }

            list($name,$extension) = explode(".",$file);
//            $filepath = "/home/www-data/soactive-wms".$variables["image_folder"].$file;
            $filepath = $dir.$file;
            if(!file_exists($filepath)){
                echo "<br/>File [".$filepath."] does not exist.";
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
                    echo "<br/>File type [".$file."] is not supported.";
                    return false;
            }

            echo "<br/>importing: $filepath";

            $variables["uuid"] = $this->imageExists($variables['styleid'], $variables['colourid'],$sortorder);
            if(!$variables["uuid"]){
                $variables["uuid"] = uuid("simg:");
                $updateRecord = false;
            }else{
                $updateRecord = true;
            }
            $variables["image"] = $this->getPicture($filepath);

            $imageTable = new phpbmsTable($this->db, "tbld:76d711ca-0412-11e1-a021-0017083b723b");
            $variables = $imageTable->prepareVariables($variables);
            $errorArray = $imageTable->verifyVariables($variables);

            if(!count($errorArray)){

                if($updateRecord){
                    $success=$imageTable->updateRecord($variables);
                }else{
                    $success=$imageTable->insertRecord($variables);
                }
                if(!$success){
                    echo "<br/>Unable to insert/update image record.";
                    return false;
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