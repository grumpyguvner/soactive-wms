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

		$bleep_statement = "SELECT id,description FROM SIZE";

		$bleep_result = $bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(-310,"Error 310.","Could Not Retrieve Sizes From Bleep Database");
			$success = false;

		} else {
			while($bleep_record=$bleep_db->fetchArray($bleep_result)){

echo "<br>".$bleep_record["id"]." '".$bleep_record["description"]."'";


				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM sizes WHERE bleepid=".((int) $bleep_record["id"]);
				$queryresult = $db->query($querystatement);

				if(!$queryresult){
					$error= new appError(-310,"Error 310.","Could Not Retrieve Sizes");
					$success = false;

				} else {
					$found = false;
					while($therecord=$db->fetchArray($queryresult)){
						$found = true;

						$sizes = new phpbmsTable($db,"tbld:863abaff-9673-d0cc-386d-695195f3e471");
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
echo " - Updated (".$therecord["uuid"].") ";
								$sizes->updateRecord($variables,NULL,true);
							} else {
echo " - Unchanged (".$therecord["uuid"].") ";
							}
						}//insert if no errors

					}//end while

					if(!$found){
						//couldn't find an existing record so create one
echo " - Inserting new record ";

						$sizes = new phpbmsTable($db,"tbld:863abaff-9673-d0cc-386d-695195f3e471");
						$therecord = $sizes->getDefaults();

						$variables = array();
						if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
						if($bleep_record["description"]) $variables["name"] = $bleep_record["description"];

						$variables = $sizes->prepareVariables($variables);
						$sizeVerify = $sizes->verifyVariables($variables);
						if(!count($sizeVerify))//check for errors
							$sizes->insertRecord($variables,NULL,false,false,true);//insert if no errors

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
