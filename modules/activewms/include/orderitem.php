<?php

include_once("include/tables.php");
include_once("include/fields.php");

if(class_exists("phpbmsTable")){

    class orderitem extends phpbmsTable{

	function orderitem($db, $tabledefid = "tbld:56be421c-1fa4-b867-23c8-aeaea63d427f", $backurl = NULL){
		parent::phpbmsTable($db, $tabledefid, $backurl);
	}//end function

	function getDefaults(){

		$therecord = parent::getDefaults();

//		$therecord["type"] = "Inventory";

		return $therecord;

	}//end method getDefaults

        function verifyVariables($variables){

            //check booleans
//            if(isset($variables["webenabled"]))
  //              if($variables["webenabled"] && $variables["webenabled"] != 1)
    //                $this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

            return parent::verifyVariables($variables);

        }//end method --verifyVariables--

	function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

		parent::updateRecord($variables, $modifiedby, $useUuid);

	}//end method --updateRecord--


	function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){


		$theid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

		return $theid;
	}//end method --insertRecord--

    }//end class

}//end if
?>
