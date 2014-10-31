<?php

include_once("include/bleep_db.php");
include_once("../../include/tables.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepDepartments{

	var $db;
	var $bleep_db;

	function importBleepDepartments($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Departments");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepDepartments--

	//This method should import new and updated records
	//from the bleep database
	function importBleepRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Departments"); //also stops execution!

		$message = "Importing Departments from Bleep";
		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);

		$bleep_statement = "SELECT `id`,`description`,`group` FROM DEPTS";

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Departments from Bleep Database","Bleep Import Departments");
		}

			while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){
				$message = $bleep_record["id"]." '".$bleep_record["description"]."'";
//				$log = new phpbmsLog($message, "BLEEP_DEPARTMENTS", NULL, $db);

				//
				//find the existing group record
				//
				$mygroupid = false;
				$myparentid = "";

				$querystatement="SELECT uuid,categoryid FROM groups WHERE bleepid=".((int) $bleep_record["group"]);
				$queryresult = $this->db->query($querystatement);

				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Groups","Bleep Import Departments");

				} else {
					while($therecord=$this->db->fetchArray($queryresult)){

						$mygroupid = $therecord["uuid"];
						$myparentid = $therecord["categoryid"];

					}//end while

				}//endif queryresult
				//
				//we should now have a groupid
				//
				if(!$mygroupid){
					$log = new phpbmsLog($message.": Could not find a corresponding group for department", "BLEEP_DEPARTMENTS", NULL, $db);
				}


				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM departments WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $this->db->query($querystatement);
				if(!$queryresult){
					$error= new appError(0,"Could Not Retrieve Departments","Bleep Import Departments");
				}

				$found = false;
				while($therecord=$this->db->fetchArray($queryresult)){
					$found = true;

					$departments = new phpbmsTable($this->db,"tbld:01f631cd-9eec-cc24-3d34-d615940897ab");
					$therecord = $departments->getRecord($therecord["uuid"],true);
					$variables = array();

					$changed = false;
				        foreach($therecord as $name => $field){

						switch($name){

							//the following variables may change
							case "name":
								$variables[$name] = $bleep_record["description"];
								if(strcmp($variables[$name],$therecord[$name])){
//$log = new phpbmsLog("description changed from ".$therecord[$name]." to ".$variables[$name], "BLEEP_DEPARTMENTS", NULL, $db);
								 $changed = true;
								}
								break;

							case "groupid":
								$variables[$name] = $mygroupid;
								if(strcmp($variables[$name],$therecord[$name])) $changed = true;
								break;

							default://copy all remaining fields over
								$variables[$name] = $field;
								break;

						}//end switch

					}//endforeach

					$variables = $departments->prepareVariables($variables);
					$departmentVerify = $departments->verifyVariables($variables);
					if(!count($departmentVerify)){//check for errors
						if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_DEPARTMENTS", NULL, $db);

							$departments->updateRecord($variables,BLEEP_IMPORTUSER,true);
						} else {
							$message .= " - Unchanged (".$therecord["uuid"].") ";
//							$log = new phpbmsLog($message, "BLEEP_DEPARTMENTS", NULL, $db);
						}
					}//insert if no errors

				}//end while

				if(!$found){
					//couldn't find an existing record so create one
					$message .= " - Inserting new record ";
					$log = new phpbmsLog($message, "BLEEP_DEPARTMENTS", NULL, $db);

					//we need to create a new cateory to match group to
					$category = new phpbmsTable($this->db,"tbld:3342a3d4-c6a2-3a38-6576-419299859561");
					$therecord = $category->getDefaults();

					$variables = array();
					if($bleep_record["description"]){
						$variables["name"] = $bleep_record["description"];
						$variables["webdisplayname"] = $variables["name"];
					}
					$variables["parentid"] = $myparentid;
					$variables["webenabled"] = true;//do we want to automatically enable?
					$variables["description"] = "automatically created by bleep import";

					$variables = $category->prepareVariables($variables);
					$categoryVerify = $category->verifyVariables($variables);
					if(!count($categoryVerify))//check for errors
						$category = $category->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors and return uuid (in array)

					//now we need to create department
					$departments = new phpbmsTable($this->db,"tbld:01f631cd-9eec-cc24-3d34-d615940897ab");
					$therecord = $departments->getDefaults();

					$variables = array();
					if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
					if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];
					if($category["uuid"]) $variables["categoryid"] = $category["uuid"];
					if($mygroupid) $variables["groupid"] = $mygroupid;

					$variables = $departments->prepareVariables($variables);
					$departmentVerify = $departments->verifyVariables($variables);
					if(!count($departmentVerify))//check for errors
						$departments->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors

				}//endif record found

			}
	}//end method --importBleepRecords-

}//end class --importBleepDepartments--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepDepartments($db);
    $clean->importBleepRecords();

}//end if
?>
