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

		$bleep_statement = "SELECT id,description FROM COLOUR";

		$bleep_result = $bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(-310,"Error 310.","Could Not Retrieve Colours From Bleep Database");
			$success = false;

		} else {
			while($bleep_record=$bleep_db->fetchArray($bleep_result)){

echo "<br>".$bleep_record["id"]." '".$bleep_record["description"]."'";


				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM colours WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $db->query($querystatement);

				if(!$queryresult){
					$error= new appError(-310,"Error 310.","Could Not Retrieve Colours");
					$success = false;

				} else {
					$found = false;
					while($therecord=$db->fetchArray($queryresult)){
						$found = true;

						$colours = new phpbmsTable($db,"tbld:433db989-89d5-dddf-2e8f-ea094862a1c4");
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
echo " - Updated (".$therecord["uuid"].") ";
								$colours->updateRecord($variables,NULL,true);
							} else {
echo " - Unchanged (".$therecord["uuid"].") ";
							}
						}//insert if no errors

					}//end while

					if(!$found){
						//couldn't find an existing record so create one
echo " - Inserting new record ";

						$colours = new phpbmsTable($db,"tbld:433db989-89d5-dddf-2e8f-ea094862a1c4");
						$therecord = $colours->getDefaults();

						$variables = array();
						if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
						if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];

						$variables = $colours->prepareVariables($variables);
						$colourVerify = $colours->verifyVariables($variables);
						if(!count($colourVerify))//check for errors
							$colours->insertRecord($variables,NULL,false,false,true);//insert if no errors

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
