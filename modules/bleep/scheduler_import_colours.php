<?php

include_once("include/bleep_db.php");
include_once("../../include/tables.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepColours{

	var $db;
	var $bleep_db;

	function importBleepColours($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Colours");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepColours--

	//This method should import new and updated records
	//from the bleep database
	function importBleepRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Colours"); //also stops execution!

		$message = "Importing Colours from Bleep";
		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);

		$bleep_statement = "SELECT `id`,`description` FROM COLOUR";

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Colours from Bleep Database","Bleep Import Colours");
		}

			while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){
				$message = $bleep_record["id"]." '".$bleep_record["description"]."'";
//				$log = new phpbmsLog($message, "BLEEP_COLOUR", NULL, $this->db);

				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM colours WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $this->db->query($querystatement);
				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Colours","Bleep Import Colours");
				}

				$found = false;
				while($therecord=$this->db->fetchArray($queryresult)){
					$found = true;

					$colours = new phpbmsTable($this->db,"tbld:433db989-89d5-dddf-2e8f-ea094862a1c4");
					$therecord = $colours->getRecord($therecord["uuid"],true);
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

					$variables = $colours->prepareVariables($variables);
					$colourVerify = $colours->verifyVariables($variables);
					if(!count($colourVerify)){//check for errors
						if($changed){
							$message .= " - Updated (".$therecord["uuid"].") ";
							$log = new phpbmsLog($message, "BLEEP_COLOUR", NULL, $this->db);

							$colours->updateRecord($variables,BLEEP_IMPORTUSER,true);
						} else {
							$message .= " - Unchanged (".$therecord["uuid"].") ";
							$log = new phpbmsLog($message, "BLEEP_COLOUR", NULL, $this->db);
						}
					}//insert if no errors

				}//end while

				if(!$found){
					//couldn't find an existing record so create one
					$message .= " - Inserting new record ";
					$log = new phpbmsLog($message, "BLEEP_COLOUR", NULL, $this->db);

					//now we need to create colour
					$colours = new phpbmsTable($this->db,"tbld:433db989-89d5-dddf-2e8f-ea094862a1c4");
					$therecord = $colours->getDefaults();

					$variables = array();
					if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
					if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];

					$variables = $colours->prepareVariables($variables);
					$colourVerify = $colours->verifyVariables($variables);
					if(!count($colourVerify))//check for errors
						$colours->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors

				}//endif record found

			}
	}//end method --importBleepRecords-

}//end class --importBleepColours--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepColours($db);
    $clean->importBleepRecords();

}//end if
?>
