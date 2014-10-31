<?php

if(class_exists("phpbmsTable")){

    class categories extends phpbmsTable{

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
                                `categoryid`,
                                `site`,
                                `webdescription`,
                                `webenabled`,
                                `webdisplayname`,
                                `weburl`,
                                `meta_title`,
                                `meta_description`,
                                `meta_keywords`,
                                `banner_image`,
                                `banner_image_name`,
                                `banner_image_type`,
                                `displayfrom`,
                                `displayuntil`,
                                `inactive`,
                                `createdby`,
                                `creationdate`,
                                `modifiedby`,
                                `modifieddate`
                        FROM
                                `category_overrides`
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

                    $therecord["apifileurl"] = $protocol.$_SERVER["SERVER_NAME"].$port.APP_PATH."/modules/activewms/categories_image.php?id=".(int)$therecord["id"];
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
                if(isset($_FILES['upload_banner_image']))
                        if($_FILES['upload_banner_image']["name"]){
                                $variables["banner_image_name"] = $_FILES['upload_banner_image']["name"];
                                $variables["banner_image_type"] = $_FILES['upload_banner_image']['type'];
                                $variables["banner_image"] = $this->getPicture("upload_banner_image");
//                                $variables["banner_image"] = "/home/activewms/images/public_html/images/categories/"; //.$this->getPicture("upload");
//                                $uploadfile=$variables["banner_image"].$variables["banner_image_name"];
//                                move_uploaded_file($_FILES['upload']['tmp_name'], $uploadfile);
                        } else {
                                unset($this->fields["banner_image_type"]);
                                unset($this->fields["banner_image_name"]);
//                                unset($this->fields["banner_image"]);
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

        function checkForValidCategoryid($categoryid){

            if((string)$categoryid != ""){
                $querystatement = "
                    SELECT
                        `id`
                    FROM
                        `categories`
                    WHERE
                        (
                            (`uuid` = '".$categoryid."')
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

            if(isset($variables["categoryid"])){

                $tempParentId = $variables["categoryid"];

                if(!$this->checkForValidCategoryid($tempParentId))
                    $this->verifyErrors[] = "The `categoryid` field does not give a valid override id.";

            }//end if

            return parent::verifyVariables($variables);

        }//end method

        function showCategoriesSelect($value){

            $id = mysql_real_escape_string($uuid);
            $value = mysql_real_escape_string($value);

            $querystatement = "
                SELECT
                    `uuid`,
                    `name`
                FROM
                    `categories`
                WHERE
                    (`inactive` = 0 OR `uuid` = '".$value."')
                ORDER BY `name`";

            $queryresult = $this->db->query($querystatement);

            ?>
                <label for="categoryid">Override for</label><br />
                <select id="categoryid" name="categoryid">
                    <option value="" <?php if($value == "") echo 'selected="selected"'?>>No Parent</option>
                    <?php

                        while($therecord = $this->db->fetchArray($queryresult)){

                            ?><option value="<?php echo $therecord["uuid"]?>" <?php if($therecord["uuid"] == $value) echo 'selected="selected"'?>><?php echo formatVariable($therecord["name"]); ?></option><?php

                        }//endwhile

                    ?>
                </select>
            <?php


        }//end function showCategoriesSelect

    }//end class

}//end if
?>
