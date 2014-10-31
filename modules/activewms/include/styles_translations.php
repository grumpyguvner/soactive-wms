<?php

include_once("include/tables.php");
include_once("include/fields.php");

if(class_exists("phpbmsTable")){

	class styles_translations extends phpbmsTable{

		function styles_translations($db, $tabledefid = "tbld:061dd57e-df95-a18b-03e4-fcfe43fad7df", $backurl = NULL){
			parent::phpbmsTable($db, $tabledefid, $backurl);
		}//end function

		/**
		 * Overriden phpbmstable function
		 */
		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["styleid"] = "";
			$therecord["site"] = "";
			$therecord["stylename"] = "";
			$therecord["description"] = "";
			$therecord["keywords"] = "";
			$therecord["webdescription"] = "";
			$therecord["inactive"] = 0;
			$therecord["ecotax"] = 0;
			$therecord["price"] = 0;
			$therecord["reduction_price"] = 0;
			$therecord["reduction_percent"] = 0;
			$therecord["link_rewrite"] = "";
			$therecord["meta_description"] = "";
			$therecord["meta_title"] = "";
			$therecord["available_now"] = "";
			$therecord["available_later"] = "";
			$therecord["wholesale_price"] = 0;
			$therecord["on-sale"] = 0;

			return $therecord;

		}//end function getDefaults

		function verifyVariables($variables){

			if(isset($variables["inactive"]))
				if($variables["inactive"] && $variables["inactive"] != 1)
					$this->verifyErrors[] = "The `inactive` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["on_sale"]))
				if($variables["on_sale"] && $variables["on_sale"] != 1)
					$this->verifyErrors[] = "The `on sale` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--

		function _commonPrepareVariables($variables){

                        $variables["inactive"] = ($variables["inactive"]==true ? 1 : 0);
                        $variables["on_sale"] = ($variables["on_sale"]==true ? 1 : 0);

			if(isset($variables["ecotax"])) $variables["ecotax"] = currencyToNumber($variables["ecotax"]);
			if(isset($variables["price"])) $variables["price"] = currencyToNumber($variables["price"]);
			if(isset($variables["reduction_price"])) $variables["reduction_price"] = currencyToNumber($variables["reduction_price"]);
			if(isset($variables["reduction_percent"])) $variables["reduction_percent"] = percentToNumber($variables["reduction_percent"]);
			if(isset($variables["wholesale_price"])) $variables["wholesale_price"] = currencyToNumber($variables["wholesale_price"]);

			return $variables;

		}//end method --_commonPrepareVariables--

		/**
		 * Overriden phpbmstable function
		 */
		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			parent::updateRecord($variables, $modifiedby, $useUuid);

			//need to reset the field information.  If they did not have rights
			// we temporarilly removed the fields to be updated.
			$this->getTableInfo();

                        // Set the style updated so that it is exported
                        $querystatement='UPDATE styles SET modifiedby = '.$_SESSION["userinfo"]["id"].', modifieddate =NOW()
                                          WHERE `uuid` ="'.$variables["styleid"].'";';
                        $queryresult = $this->db->query($querystatement);
                        if(!$queryresult){
                                $error= new appError(0,"Could Not Update style to mark as changed.","STYLE UPDATE");
                        }else{
				return TRUE;
			}

		}//end function updateRecord


		/**
		 * Overriden phpbmstable function
		 */
		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($createdby === NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

                        // Set the style updated so that it is exported
                        $querystatement='UPDATE styles SET modifiedby = '.$_SESSION["userinfo"]["id"].', modifieddate =NOW()
                                          WHERE `uuid` ="'.$variables["styleid"].'";';
                        $queryresult = $this->db->query($querystatement);
                        if(!$queryresult){
                                $error= new appError(0,"Could Not Update style to mark as changed.","STYLE UPDATE");
                        }

			return $newid;

		}//end function insertRecord

	}//end class styles_translations

}//end if
?>
