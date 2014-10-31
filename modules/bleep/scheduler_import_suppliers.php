<?php

include_once("include/bleep_db.php");
include_once("../../include/tables.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepSuppliers{

	var $db;
	var $bleep_db;

	function importBleepSuppliers($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Suppliers");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepSuppliers--

	//This method should import new and updated records
	//from the bleep database
	function importBleepRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Suppliers"); //also stops execution!

		$message = "Importing Suppliers from Bleep";
		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);

		$bleep_statement = "SELECT `id`,`description` FROM supplier";

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Suppliers from Bleep Database","Bleep Import Suppliers");
		}

			while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){
				$message = $bleep_record["id"]." '".$bleep_record["description"]."'";
//				$log = new phpbmsLog($message, "BLEEP_SUPPLIER", NULL, $this->db);

				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM suppliers WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $this->db->query($querystatement);
				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Suppliers","Bleep Import Suppliers");
				}

				$found = false;
				while($therecord=$this->db->fetchArray($queryresult)){
					$found = true;

					$suppliers = new phpbmsTable($this->db,"tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82");
					$therecord = $suppliers->getRecord($therecord["uuid"],true);
					$variables = array();

					$changed = false;
				        foreach($therecord as $name => $field){

						switch($name){

							//the following variables may change
							case "name":
								$variables[$name] = $bleep_record["description"];
								if(strcmp($variables[$name],$therecord[$name])) $changed = true;
								break;

							default://copy all remaining fields over
								$variables[$name] = $field;
								break;

						}//end switch

					}//endforeach

					$variables = $suppliers->prepareVariables($variables);
					$supplierVerify = $suppliers->verifyVariables($variables);
					if(!count($supplierVerify)){//check for errors
						if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_SUPPLIER", NULL, $this->db);

							$suppliers->updateRecord($variables,BLEEP_IMPORTUSER,true);
						} else {
							$message .= " - Unchanged (".$therecord["uuid"].") ";
//							$log = new phpbmsLog($message, "BLEEP_SUPPLIER", NULL, $this->db);
						}
					}//insert if no errors

				}//end while

				if(!$found){
					//couldn't find an existing record so create one
					$message .= " - Inserting new record ";
					$log = new phpbmsLog($message, "BLEEP_SUPPLIER", NULL, $this->db);

					//now we need to create supplier
					$suppliers = new phpbmsTable($this->db,"tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82");
					$therecord = $suppliers->getDefaults();

					$variables = array();
					if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
					if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];

					$variables = $suppliers->prepareVariables($variables);
					$supplierVerify = $suppliers->verifyVariables($variables);
					if(!count($supplierVerify))//check for errors
						$suppliers->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors

				}//endif record found

			}
	}//end method --importBleepRecords-

}//end class --importBleepSuppliers--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepSuppliers($db);
    $clean->importBleepRecords();

}//end if
?>
