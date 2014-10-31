<?php

include_once("include/bleep_db.php");
include_once("../../include/tables.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepSizes{

	var $db;
	var $bleep_db;

	function importBleepSizes($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Sizes");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepSizes--

	//This method should import new and updated records
	//from the bleep database
	function importBleepRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Sizes"); //also stops execution!

		$message = "Importing Sizes from Bleep";
		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);

		$bleep_statement = "SELECT `id`,`description` FROM SIZE";

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Sizes from Bleep Database","Bleep Import Sizes");
		}

			while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){
				$message = $bleep_record["id"]." '".$bleep_record["description"]."'";
//				$log = new phpbmsLog($message, "BLEEP_SIZE", NULL, $this->db);

				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM sizes WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $this->db->query($querystatement);
				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Sizes","Bleep Import Sizes");
				}

				$found = false;
				while($therecord=$this->db->fetchArray($queryresult)){
					$found = true;

					$sizes = new phpbmsTable($this->db,"tbld:863abaff-9673-d0cc-386d-695195f3e471");
					$therecord = $sizes->getRecord($therecord["uuid"],true);
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

					$variables = $sizes->prepareVariables($variables);
					$sizeVerify = $sizes->verifyVariables($variables);
					if(!count($sizeVerify)){//check for errors
						if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_SIZE", NULL, $this->db);

							$sizes->updateRecord($variables,BLEEP_IMPORTUSER,true);
						} else {
							$message .= " - Unchanged (".$therecord["uuid"].") ";
//							$log = new phpbmsLog($message, "BLEEP_SIZE", NULL, $this->db);
						}
					}//insert if no errors

				}//end while

				if(!$found){
					//couldn't find an existing record so create one
					$message .= " - Inserting new record ";
					$log = new phpbmsLog($message, "BLEEP_SIZE", NULL, $this->db);

					//now we need to create size
					$sizes = new phpbmsTable($this->db,"tbld:863abaff-9673-d0cc-386d-695195f3e471");
					$therecord = $sizes->getDefaults();

					$variables = array();
					if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
					if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];

					$variables = $sizes->prepareVariables($variables);
					$sizeVerify = $sizes->verifyVariables($variables);
					if(!count($sizeVerify))//check for errors
						$sizes->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors

				}//endif record found

			}
	}//end method --importBleepRecords-

}//end class --importBleepSizes--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepSizes($db);
    $clean->importBleepRecords();

}//end if
?>
