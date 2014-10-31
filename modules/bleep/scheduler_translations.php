<?php

include_once("include/google_translate.php");

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class bleepTranslations{

	var $db;

	function bleepTranslations($db){
		$this->db = $db;

	}//end method --bleepTranslations--

	//This method should import new and updated records
	//from the bleep database
	function translateRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Translations"); //also stops execution!

//		$message = "Bleep Import Styles: Hello World (number)";
//		$log = new phpbmsLog($message, "SCHEDULER");

		//////////////////////////////////////////////////////////////////////////////////////////
		//                                                                                      //
		//                                                                                      //
		//////////////////////////////////////////////////////////////////////////////////////////

		//random sleep time
//		$sleep = rand(0,180);
//$log = new phpbmsLog("Sleep=".$sleep, "DEBUG TRANSLATE");
//		sleep($sleep);

		//and lets set a random throttle
//		$throttle = "100";
//		$throttle = strval(rand(0,3));
$log = new phpbmsLog("Throttle=".$throttle, "DEBUG TRANSLATE");
                if(defined("BLEEP_TRANSLATIONTHROTTLE"))
                    $throttle = BLEEP_TRANSLATIONTHROTTLE;


		$source_statement = "SELECT
					styles_translations.uuid AS uuid,
					styles.uuid AS styleid,
					styles.stylenumber AS stylenumber,
					styles.stylename AS stylename,
					styles.description AS description,
					styles.webdescription AS webdescription
					FROM styles
					LEFT JOIN styles_translations
						ON (styles_translations.styleid=styles.uuid AND styles_translations.site='www.attractive.fr' AND styles_translations.inactive=0)
					WHERE (styles.webenabled=1)
					  AND (styles_translations.id IS NULL OR
                                               styles.translationrequired>styles_translations.modifieddate)
					ORDER BY styles_translations.modifieddate";
		if($throttle!="")
			$source_statement .= " LIMIT ".$throttle;
$log = new phpbmsLog($source_statement, "DEBUG TRANSLATE");

		$source_result = $this->db->query($source_statement);

		if(!$source_result){
			$error= new appError(0,"Could Not Retrieve Styles from Database","Bleep Translations");
		}

		while($source_record=$this->db->fetchArray($source_result)){

			$message = $source_record["stylenumber"]." '".$source_record["stylename"]."' ";
//			$log = new phpbmsLog($message, "BLEEP_TRANSLATIONS");
$log = new phpbmsLog("bleep translation:".$translate_record["uuid"]." - removing this causes an ERROR!", "TODO");

			//find the existing record (if one exists)
			$querystatement="SELECT uuid FROM styles_translations WHERE ( styleid='".$source_record["styleid"]."' AND site='www.attractive.fr' AND inactive=0)";
			$queryresult = $this->db->query($querystatement);
			if(!$queryresult){
				$error= new appError(0,"Could Not Retrieve Style Translations","Bleep Translations");
			}
	
			$found = false;
			while($therecord=$this->db->fetchArray($queryresult)){
				$found = true;
				$myuuid = $therecord["uuid"];
			
				$translations = new phpbmsTable($this->db,"tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb");
				
				$therecord = $translations->getRecord($therecord["uuid"],true);
				$variables = array();

				$changed = false;







				$changed = true;






				$frStyleName = "";
				$frDescription = "";
				$frWebDescription = "";

			        foreach($therecord as $name => $field){

					switch($name){

						//the following variables may change
						case "stylename":
							$translate = new GoogleTranslateApi();
							if ($source_record["stylename"]){
								$frStyleName = $translate->translate($source_record["stylename"]);
								if($translate->DebugStatus!=200){
									$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
								}
							}
							$variables[$name] = $frStyleName;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "name ";
								$changed = true;
							}
							break;

						case "description":
							$translate = new GoogleTranslateApi();
							if ($source_record["description"]){
								$frDescription = $translate->translate($source_record["description"]);
								if($translate->DebugStatus!=200){
									$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
								}
							}
							$variables[$name] = $frDescription;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "description ";
								$changed = true;
							}
							break;

						case "webdescription":
							$translate = new GoogleTranslateApi();
							if ($source_record["webdescription"]){
								$frWebDescription = $translate->translate($source_record["webdescription"]);
								if($translate->DebugStatus!=200){
									$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
								}
							}
							$variables[$name] = $frWebDescription;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "web description ";
								$changed = true;
							}
							break;

						default://copy all remaining fields over
							$variables[$name] = $field;
							break;

					}//end switch

				}//endforeach

//$log = new phpbmsLog("style 7", "DEBUG TRANSLATE");
				$variables = $translations->prepareVariables($variables);
				$styleVerify = $translations->verifyVariables($variables);
				if(!count($styleVerify)){//check for errors
					if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_TRANSLATIONS");
						$translations->updateRecord($variables,BLEEP_IMPORTUSER,true);
					} else {
						$message .= " - Unchanged (".$therecord["uuid"].") ";
//						$log = new phpbmsLog($message, "BLEEP_TRANSLATIONS");
					}
				}//insert if no errors
			}

			if(!$found){
				//couldn't find an existing record so create one
				$message .= " - Inserting new record ";
				$log = new phpbmsLog($message, "BLEEP_TRANSLATIONS");
				$changed = true; //set the changed flag so that bleep_lastchanged gets set!

				//now we need to create translation
				$translations = new phpbmsTable($this->db,"tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb");
				$therecord = $translations->getDefaults();

				$frStyleName = "";
				$frDescription = "";
				$frWebDescription = "";

				$variables = array();
				//load values from the bleep record:
				if($source_record["styleid"]) $variables["styleid"] = $source_record["styleid"];
				$translate = new GoogleTranslateApi();
				if ($source_record["stylename"]){
					$frStyleName = $translate->translate($source_record["stylename"]);
					if($translate->DebugStatus!=200){
						$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
					}
				}
				$variables["stylename"] = $frStyleName;
				$translate = new GoogleTranslateApi();
				if ($source_record["description"]){
					$frDescription = $translate->translate($source_record["description"]);
					if($translate->DebugStatus!=200){
						$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
					}
				}
				$variables["description"] = $frDescription;
				$translate = new GoogleTranslateApi();
				if ($source_record["webdescription"]){
					$frWebDescription = $translate->translate($source_record["webdescription"]);
					if($translate->DebugStatus!=200){
						$error= new appError(0,"Google Translation Error: ".$translate->DebugMsg,"Bleep Translations");
					}
				}
				$variables["webdescription"] = $frWebDescription;

				//load other default values:
				$variables["site"] = "www.attractive.fr";

				$variables = $translations->prepareVariables($variables);
				$styleVerify = $translations->verifyVariables($variables);
				if(!count($styleVerify))//check for errors
					$therecord = $translations->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors
				$myuuid = $therecord["uuid"];
			}//endif record found

		}//end while loop

//$log = new phpbmsLog("style 14", "DEBUG TRANSLATE");
	}//end method --bleepTranslations-

}//end class --bleepTranslations--

if(!isset($noOutput) && isset($db)){

    $clean = new bleepTranslations($db);
    $clean->translateRecords();

}//end if
?>
