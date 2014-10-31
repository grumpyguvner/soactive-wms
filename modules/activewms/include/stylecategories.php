<?php

if(class_exists("phpbmsTable")){

    class stylecategories extends phpbmsTable{

        function checkForValidParentid($uuid, $parentid){

            if((string)$parentid != ""){
                $querystatement = "
                    SELECT
                        `id`
                    FROM
                        `stylecategories`
                    WHERE
                        (
                            `uuid` != '".$uuid."'
                            AND (`parentid` = '' OR `parentid` != '".$uuid."')
                            AND (`uuid` = '".$parentid."')
                        )
                    ";

                $queryresult = $this->db->query($querystatement);

                return $this->db->numRows($queryresult);
            }else{
                return true;
            }//end if

        }//end function

        function verifyVariables($variables){

            //check booleans
            if(isset($variables["webenabled"]))
                if($variables["webenabled"] && $variables["webenabled"] != 1)
                    $this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

            if(isset($variables["parentid"])){

                $tempParentId = $variables["parentid"];

                $tempUUID = "";
                if(isset($variables["uuid"]))
                    $tempUUID = $variables["uuid"];

                if(!$this->checkForValidParentid($tempUUID, $tempParentId))
                    $this->verifyErrors[] = "The `parentid` field does not give a valid parent id.";

            }//end if

            return parent::verifyVariables($variables);

        }//end method

        function showParentsSelect($uuid = "", $value){

            $id = mysql_real_escape_string($uuid);
            $value = mysql_real_escape_string($value);

            $querystatement = "
                SELECT
                    `uuid`,
                    `name`
                FROM
                    `stylecategories`
                WHERE
                    `uuid` != '".$uuid."'
                    AND (`parentid` = '' OR `parentid` != '".$uuid."')
                    AND (`inactive` = 0 OR `uuid` = '".$value."')";

            $queryresult = $this->db->query($querystatement);

            ?>
                <label for="parentid">Parent Category</label><br />
                <select id="parentid" name="parentid">
                    <option value="" <?php if($value == "") echo 'selected="selected"'?>>No Parent</option>
                    <?php

                        while($therecord = $this->db->fetchArray($queryresult)){

                            ?><option value="<?php echo $therecord["uuid"]?>" <?php if($therecord["uuid"] == $value) echo 'selected="selected"'?>><?php echo formatVariable($therecord["name"]); ?></option><?php

                        }//endwhile

                    ?>
                </select>
            <?php


        }//end function showParentsSelect


        function showAttractiveSelect($value){

            $value = mysql_real_escape_string($value);

            $querystatement = "
                SELECT
                    `id`,
                    `name`
                FROM
                    `attractive_categories`";

            $queryresult = $this->db->query($querystatement);

            ?>
                <label for="attractiveid">Attractive Category</label><br />
                <select id="attractiveid" name="attractiveid">
                    <option value="" <?php if($value == "") echo 'selected="selected"'?>>None</option>
                    <?php

                        while($therecord = $this->db->fetchArray($queryresult)){

                            ?><option value="<?php echo $therecord["id"]?>" <?php if($therecord["id"] == $value) echo 'selected="selected"'?>><?php echo formatVariable($therecord["name"]); ?></option><?php

                        }//endwhile

                    ?>
                </select>
            <?php


        }//end function showAttractiveSelect

    }//end class

}//end if
?>
