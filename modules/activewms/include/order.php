<?php

include_once("include/tables.php");
include_once("include/fields.php");

if(class_exists("phpbmsTable")){

    class order extends phpbmsTable{

	function order($db, $tabledefid = "tbld:3c9b1ba3-0eb4-98b6-7f63-020faa96aa19", $backurl = NULL){
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

        function refreshTotals($orderid, $useUuid = false){

            //find the existing record (if one exists)
            $orderrecord = parent::getRecord($orderid, $useUuid);
            if($orderrecord["id"]===NULL){
                    return array();
            }

            $updatestatement='UPDATE `orders`
                                SET `totalweight` = (SELECT SUM(`quantity`*`unitweight`) FROM `orderitems` WHERE `orderitems`.`orderid` = `orders`.`uuid`),
                                    `totalcost`   = (SELECT SUM(`quantity`*`unitcost`)   FROM `orderitems` WHERE `orderitems`.`orderid` = `orders`.`uuid`),
                                    `totalti`     = (`shipping` + (SELECT SUM(`quantity`*`unitprice`)   FROM `orderitems` WHERE `orderitems`.`orderid` = `orders`.`uuid`))
                              WHERE `orders`.`uuid` = \''.$orderrecord["uuid"].'\'';
            $updateresult = $this->db->query($updatestatement);
//            if(!$updateresult){
//                    return array();
//            }

            //refetch the updated record
            $orderrecord = parent::getRecord($orderid, $useUuid);
            return $orderrecord;

        }

        function getTotalDetailLines($orderid){

            $mySql='SELECT COUNT(`orderitems`.`id`)
                        FROM `orderitems`
                        WHERE `orderitems`.`orderid` = \''.$orderid.'\'';
            $myResult = $this->db->query($mySql);
            if (mysql_num_rows($myResult)==0)
                return 0;
            else
                return mysql_result($myResult,0);

         }

        function getTotalQuantity($orderid){

            $mySql='SELECT SUM(`orderitems`.`quantity`)
                        FROM `orderitems`
                        WHERE `orderitems`.`orderid` = \''.$orderid.'\'';
            $myResult = $this->db->query($mySql);
            if (mysql_num_rows($myResult)==0)
                return 0;
            else
                return mysql_result($myResult,0);

        }

        function getTotalDiscount($orderid){

            $mySql='SELECT SUM(`orderitems`.`quantity`*`orderitems`.`unitdiscount`)
                        FROM `orderitems`
                        WHERE `orderitems`.`orderid` = \''.$orderid.'\'';
            $myResult = $this->db->query($mySql);
            if (mysql_num_rows($myResult)==0)
                return 0;
            else
                return mysql_result($myResult,0);

        }

}//end class


}//end if
?>
