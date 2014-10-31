<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$success = true;

	$querystatement="SELECT uuid, stylenumber FROM styles WHERE inactive=0";//we aren't concerned with inactive records
	$queryresult = $db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Styles");
		$success = false;

	} else {
		while($myrecord=$db->fetchArray($queryresult)){
echo "<br>".$myrecord["uuid"]." stylenumber=".$myrecord["stylenumber"]." ";

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



				$bleep_statement="SELECT id FROM STYLES WHERE id=".$myrecord["stylenumber"];
				$bleep_result = $bleep_db->query($bleep_statement);

				if(!$bleep_result){
					$error= new appError(-310,"Error 310.","Could Not Retrieve Style Table from Bleep");
					$success = false;

				} else {
					$found = false;
					while($bleep_record=$bleep_db->fetchArray($bleep_result)){
						$found = true;
						//Do we want to do anything with the record?
						//Multiple records should not be possible
					}//end while

					if(!$found){

						$styles = new phpbmsTable($db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42");

						$therecord = $styles->getRecord($myrecord["uuid"],true);
						$variables = array();

					        foreach($therecord as $name => $field){

							switch($name){

								case "inactive"://we dont delete the record,
								                // just mark as inactive
									$variables["inactive"] = true;
									break;

								default://copy all remaining fields over
									$variables[$name] = $field;
									break;

							}//end switch

						}//endforeach

						$variables = $styles->prepareVariables($variables);
						$departmentVerify = $styles->verifyVariables($variables);
						if(!count($departmentVerify)){//check for errors
echo " - deactivating ";
							$styles->updateRecord($variables,NULL,true);
						}//update if no errors
					} else {
echo " - still active ";
					}

				}

			}//endif database connect

		}//end while loop
	}

	if(!$success){
		echo "<br>Update Failed!";
	} else {
		echo "<br>Update Successful!";
	}//endif loadBleepDB

?>
