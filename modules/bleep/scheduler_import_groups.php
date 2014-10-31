<?php

include_once("include/bleep_db.php");
include_once("../../include/tables.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepGroups{

	var $db;
	var $bleep_db;

	function importBleepGroups($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Groups");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepGroups--

	//This method should import new and updated records
	//from the bleep database
	function importBleepRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Groups"); //also stops execution!

		$message = "Importing Groups from Bleep";
		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);

		$bleep_statement = "SELECT `id`,`description` FROM GROUPS";

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Groups from Bleep Database","Bleep Import Groups");
		}

			while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){
				$message = $bleep_record["id"]." '".$bleep_record["description"]."'";
//				$log = new phpbmsLog($message, "BLEEP_GROUPS", NULL, $this->db);

				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM groups WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $this->db->query($querystatement);
				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Groups","Bleep Import Groups");
				}

				$found = false;
				while($therecord=$this->db->fetchArray($queryresult)){
					$found = true;

					$groups = new phpbmsTable($this->db,"tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411");
					$therecord = $groups->getRecord($therecord["uuid"],true);
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

					$variables = $groups->prepareVariables($variables);
					$groupVerify = $groups->verifyVariables($variables);
					if(!count($groupVerify)){//check for errors
						if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_GROUPS", NULL, $this->db);

							$groups->updateRecord($variables,BLEEP_IMPORTUSER,true);
						} else {
							$message .= " - Unchanged (".$therecord["uuid"].") ";
//							$log = new phpbmsLog($message, "BLEEP_GROUPS", NULL, $this->db);
						}
					}//insert if no errors

				}//end while

				if(!$found){
					//couldn't find an existing record so create one
					$message .= " - Inserting new record ";
					$log = new phpbmsLog($message, "BLEEP_GROUPS", NULL, $this->db);

					//we need to create a new cateory to match group to
					$category = new phpbmsTable($this->db,"tbld:3342a3d4-c6a2-3a38-6576-419299859561");
					$therecord = $category->getDefaults();

					$variables = array();
					if($bleep_record["description"]){
						$variables["name"] = $bleep_record["description"];
						$variables["webdisplayname"] = $variables["name"];
					}
					$variables["parentid"] = ""; //groups are top level
					$variables["webenabled"] = true;//do we want to automatically enable?
					$variables["description"] = "automatically created by bleep import";

					$variables = $category->prepareVariables($variables);
					$categoryVerify = $category->verifyVariables($variables);
					if(!count($categoryVerify))//check for errors
						$category = $category->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors and return uuid (in array)

					//now we need to create group
					$groups = new phpbmsTable($this->db,"tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411");
					$therecord = $groups->getDefaults();

					$variables = array();
					if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
					if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];
					if($category["uuid"]) $variables["categoryid"] = $category["uuid"];

					$variables = $groups->prepareVariables($variables);
					$groupVerify = $groups->verifyVariables($variables);
					if(!count($groupVerify))//check for errors
						$groups->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors

				}//endif record found

			}
	}//end method --importBleepRecords-

}//end class --importBleepGroups--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepGroups($db);
    $clean->importBleepRecords();

}//end if
?>
