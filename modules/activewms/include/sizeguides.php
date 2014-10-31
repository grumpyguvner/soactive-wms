<?php

if(class_exists("phpbmsTable")){

    class sizeguides extends phpbmsTable{

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
                                `parentid`,
                                `displayorder`,
                                `webdescription`,
                                `webenabled`,
                                `webdisplayname`,
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
                                `sizeguides`
                        WHERE
                                ".$whereclause;

                $queryresult = $this->db->query($querystatement);

                if($this->db->numRows($queryresult)){
                    $therecord = $this->db->fetchArray($queryresult);
                }else
                    $therecord = $this-> getDefaults();

                return $therecord;

        }//end function --getRecord--

        function prepareVariables($variables){

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

        function verifyVariables($variables){

            //check booleans
            if(isset($variables["webenabled"]))
                if($variables["webenabled"] && $variables["webenabled"] != 1)
                    $this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

            return parent::verifyVariables($variables);

        }//end method

    }//end class

}//end if
?>
