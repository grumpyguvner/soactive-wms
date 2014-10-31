<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$success = true;

	include_once("include/bleep_db.php");
	$bleep_db = new bleep_db();

	if($bleep_db==NULL){
		$error=new appError(-310,"","Database not loaded");
		$success = false;

	} else {
		$bleep_db->logError = false;
		$bleep_db->stopOnError = false;

		$bleep_db->setEncoding($encoding);

		$bleep_db->logError = true;

		$bleep_statement = "SELECT id,description FROM GROUPS";

		$bleep_result = $bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(-310,"Error 310.","Could Not Retrieve Groups From Bleep Database");
			$success = false;

		} else {
			while($bleep_record=$bleep_db->fetchArray($bleep_result)){

echo "<br>".$bleep_record["id"]." '".$bleep_record["description"]."'";


				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM groups WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $db->query($querystatement);

				if(!$queryresult){
					$error= new appError(-310,"Error 310.","Could Not Retrieve Groups");
					$success = false;

				} else {
					$found = false;
					while($therecord=$db->fetchArray($queryresult)){
						$found = true;

						$groups = new phpbmsTable($db,"tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411");
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
echo " - Updated (".$therecord["uuid"].") ";
								$groups->updateRecord($variables,NULL,true);
							} else {
echo " - Unchanged (".$therecord["uuid"].") ";
							}
						}//insert if no errors

					}//end while

					if(!$found){
						//couldn't find an existing record so create one
echo " - Inserting new record ";
						//we need to create a new cateory to match group to
						$category = new phpbmsTable($db,"tbld:3342a3d4-c6a2-3a38-6576-419299859561");
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
							$category = $category->insertRecord($variables,NULL,false,false,true);//insert if no errors and return uuid (in array)

						//now we need to create group
						$groups = new phpbmsTable($db,"tbld:f3a8708e-8dc8-09cc-667e-ccabc43f5411");
						$therecord = $groups->getDefaults();

						$variables = array();
						if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
						if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];
						if($category["uuid"]) $variables["categoryid"] = $category["uuid"];

						$variables = $groups->prepareVariables($variables);
						$groupVerify = $groups->verifyVariables($variables);
						if(!count($groupVerify))//check for errors
							$groups->insertRecord($variables,NULL,false,false,true);//insert if no errors

					}

				}//endif queryresult

			}//end while

		}//endif bleep_result

	}//endif database connect

	if(!$success){
		echo "<br>Import Failed!";
	} else {
		echo "<br>Import Successful!";
	}//endif loadBleepDB

?>
