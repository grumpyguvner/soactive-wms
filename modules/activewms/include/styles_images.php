<?php

if(class_exists("phpbmsTable")){

    class styles_images extends phpbmsTable{

        function getDefaults(){

                $therecord = parent::getDefaults();

                $therecord["apifileurl"] = "";

                return $therecord;

        }//end function --getDefaults

        function getRecord($id, $useUuid = false){

                if($useUuid){

                        $id = mysql_real_escape_string($id);
                        $whereclause = "`uuid` = '".$id."'";

                } else {

                        $id = (int) $id;
                        $whereclause = "`id` = ".$id;

                }//endif

                $querystatement = "
                        SELECT
                                `id`,
                                `uuid`,
                                `name`,
                                `alt_text`,
                                `styleid`,
                                `colourid`,
                                `displayorder`,
                                `image`,
                                `image_name`,
                                `image_type`,
                                `webenabled`,
                                `inactive`,
                                `createdby`,
                                `creationdate`,
                                `modifiedby`,
                                `modifieddate`,
                                `custom1`,
                                `custom2`,
                                `custom3`,
                                `custom4`,
                                `custom5`,
                                `custom6`,
                                `custom7`,
                                `custom8`
                        FROM
                                `styles_images`
                        WHERE
                                ".$whereclause;

                $queryresult = $this->db->query($querystatement);

                if($this->db->numRows($queryresult)){
                    $therecord = $this->db->fetchArray($queryresult);

                    if(!empty($_SERVER["HTTPS"]))
                            $protocol = "https://";
                    else
                            $protocol = "http://";


                    if( ($_SERVER["SERVER_PORT"] == "443" && !empty($_SERVER["HTTPS"])) || ($_SERVER["SERVER_PORT"] == "80" && empty($_SERVER["HTTPS"])) )
                            $port = "";
                    else
                            $port = ":".$_SERVER["SEVER_PORT"];

                    $therecord["apifileurl"] = $protocol.$_SERVER["SERVER_NAME"].$port.APP_PATH."/modules/activewms/styles_image.php?id=".(int)$therecord["id"];
                }else
                    $therecord = $this-> getDefaults();

                return $therecord;

        }//end function --getRecord--

        function getPicture($name){

                if (function_exists('file_get_contents')) {
                        $file = addslashes(file_get_contents($_FILES[$name]['tmp_name']));
                        $file = file_get_contents($_FILES[$name]['tmp_name']);
                } else {
                        // If using PHP < 4.3.0 use the following:
                        $file = addslashes(fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name'])));
                        $file = fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name']));
                }

                return $file;
        }

        function prepareVariables($variables){
                if(isset($_FILES['upload_image']))
                        if($_FILES['upload_image']["name"]){
                                $variables["image_name"] = $_FILES['upload_image']["name"];
                                $variables["image_type"] = $_FILES['upload_image']['type'];
                                $variables["image"] = $this->getPicture("upload_image");
//                                $variables["image"] = "/home/activewms/images/public_html/images/styles_images/"; //.$this->getPicture("upload");
//                                $uploadfile=$variables["image"].$variables["image_name"];
//                                move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile);
                        } else {
                                unset($this->fields["image_type"]);
                                unset($this->fields["image_name"]);
//                                unset($this->fields["image"]);
                        }//end if

                return parent::prepareVariables($variables);

        }//end function


        function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

                $thereturn = parent::updateRecord($variables, $modifiedby, $useUuid);

                //restore the fields
                $this->getTableInfo();


                return $thereturn;
        }//end method


        function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

                $newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

                //restore the fields
                $this->getTableInfo();

                return $newid;

        }//end method

        function checkForValidStyleid($uuid, $styleid){

            if((string)$styleid != ""){
                $querystatement = "
                    SELECT
                        `id`
                    FROM
                        `styles`
                    WHERE
                        (
                            `uuid` = '".$styleid."'
                        )
                    ";

                $queryresult = $this->db->query($querystatement);

                return $this->db->numRows($queryresult);
            }else{
                return true;
            }//end if

        }//end function

        function verifyVariables($variables){

            if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
                $poidsMax = ini_get('post_max_size');
                $this->verifyErrors[] = "file too big! Your file is too big, the maximum size is $poidsMax.";
            }

            //check booleans
            if(isset($variables["webenabled"]))
                if($variables["webenabled"] && $variables["webenabled"] != 1)
                    $this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

            if(isset($variables["styleid"])){

                $tempStyleId = $variables["styleid"];

                $tempUUID = "";
                if(isset($variables["uuid"]))
                    $tempUUID = $variables["uuid"];

                if(!$this->checkForValidStyleid($tempUUID, $tempStyleId))
                    $this->verifyErrors[] = "The `styleid` field does not give a valid style.";

            }//end if

            return parent::verifyVariables($variables);

        }//end method

    }//end class

}//end if
?>
