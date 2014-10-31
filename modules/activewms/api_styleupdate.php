<?php
/////////////////////////////////////////////
//   IMPORT PRODUCT INFO TO ACTIVEWMS      //
/////////////////////////////////////////////
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");
include("include/styles.php");
include("include/styles_translations.php");

//$now = gmdate('Y-m-d H:i', strtotime('now'));

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class api_styleupdate{

	function api_styleupdate($db){
		$this->db = $db;
	}//end method --api_styleupdate--

	//This method should import new and updated records
	//from the edi database
	function importHeader($postData){

		$message = "Style update received from ".$postData["source"];
		$message .=" ref ".$postData["stylenumber"];
		$message .=" date ".$postData["modifieddate"];
	
		$log = new phpbmsLog($message, "EDI STYLE UPDATE", NULL, $this->db);

		//we can only add detail for a style which exists
$log = new phpbmsLog("DEBUG: CHECKING TO SEE IF STYLE EXISTS (".$postData["stylenumber"].")", "EDI STYLE UPDATE", NULL, $this->db);
		$style = new styles($this->db);
		$stylerecord = $style->getByReference($postData["stylenumber"]);
		//we can only continue if uuid is valid
		if($stylerecord["id"]===NULL){
			$error= new appError(0,"Invalid Style Number ".$postData["stylenumber"], "EDI STYLE UPDATE");
		}

		//find the existing record (if one exists)
		$querystatement='SELECT * FROM `styles_translations`
                                  WHERE `styleid`=\''.$stylerecord["uuid"].'\'
                                    AND `site`=\''.$postData["source"].'\'
                                    AND `inactive`=0';
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult){
			$error= new appError(0,"Could Not Retrieve style site data","EDI STYLE UPDATE");
		}

		$found = false;
		while($therecord=$this->db->fetchArray($queryresult)){
			$found = true;

			$changed = false;
			foreach($therecord as $name => $field){

				switch($name){

					//the following variables may change
					case "stylename":
					case "description":
					case "keywords":
					case "webdescription":
					case "link_rewrite":
					case "meta_description":
					case "meta_title":
					case "available_now":
					case "available_later":
						$variables[$name] = $postData[$name];
						if(strcmp($variables[$name],$therecord[$name])) $changed = true;
						break;

					case "ecotax": //non-string objects
					case "price":
					case "reduction_price":
					case "reduction_percent":
					case "on_sale":
					case "wholesale_price":
						$variables[$name] = $postData[$name];
						if($variables[$name]!=$therecord[$name]) $changed = true;
						break;

//					case "modifieddate": //date objects
//						$variables[$name] = formatFromSQLDatetime($postData[$name]);
//						if($variables[$name]!=$therecord[$name]) $changed = true;
//						break;

					default://copy all remaining fields over
						$variables[$name] = $field;
						break;

				}//end switch

			}//endforeach

                        $updateRecord = new styles_translations($this->db);

			$variables = $updateRecord->prepareVariables($variables);
			$styleVerify = $updateRecord->verifyVariables($variables);
			if(!count($styleVerify)){//check for errors
				if($changed){
                                        $message .= " - Updated (".$therecord["uuid"].") ";
                                        $log = new phpbmsLog($message, "EDI STYLE UPDATE", NULL, $this->db);

                                        if (!$updateRecord->updateRecord($variables,BLEEP_IMPORTUSER,true)){
                                            echo "ERROR UPDATING RECORD!";
                                            return false;
                                        }
				} else {
					$message .= " - Unchanged (".$therecord["uuid"].") ";
				//	$log = new phpbmsLog($message, "EDI STYLE UPDATE", NULL, $this->db);
				}
	
				//If everything ok then return uuid
				echo "OK".$therecord["uuid"];
			}//insert if no errors

		}//end while

		if(!$found){
			//couldn't find an existing record so create one
			$message .= " - Inserting new record ";
			$log = new phpbmsLog($message, "EDI STYLE UPDATE", NULL, $this->db);

			$newRecord = new styles_translations($this->db);
			$variables = $newRecord->getDefaults();

			if($stylerecord["id"])
                                $variables["styleid"] = $stylerecord["uuid"];
			if($postData["source"])
				$variables["site"] = $postData["source"];
			if($postData["stylenumber"])
				$variables["stylenumber"] = $postData["stylenumber"];

                        if($postData["stylename"])
				$variables["stylename"] = $postData["stylename"];
                        if($postData["description"])
				$variables["description"] = $postData["description"];
                        if($postData["keywords"])
				$variables["keywords"] = $postData["keywords"];
                        if($postData["webdescription"])
				$variables["webdescription"] = $postData["webdescription"];
                        if($postData["link_rewrite"])
				$variables["link_rewrite"] = $postData["link_rewrite"];
                        if($postData["meta_description"])
				$variables["meta_description"] = $postData["meta_description"];
                        if($postData["meta_title"])
				$variables["meta_title"] = $postData["meta_title"];
                        if($postData["available_now"])
				$variables["available_now"] = $postData["available_now"];
                        if($postData["available_later"])
				$variables["available_later"] = $postData["available_later"];
                        if($postData["ecotax"])
				$variables["ecotax"] = $postData["ecotax"];
                        if($postData["price"])
				$variables["price"] = $postData["price"];
			//Make sure only one of reduction_price or reduction_percent is set.
//                        if($postData["reduction_percent"]){
//				$variables["reduction_percent"] = $postData["reduction_percent"];
//				$variables["reduction_price"] = 0;
//			}elseif($postData["reduction_price"]){
//				$variables["reduction_price"] = $postData["reduction_price"];
//				$variables["reduction_percent"] = 0;
//			}
			$variables["reduction_percent"] = $postData["reduction_percent"];
			$variables["reduction_price"] = $postData["reduction_price"];
			$variables["on_sale"] = $postData["on_sale"];
                        if($postData["wholesale_price"])
				$variables["wholesale_price"] = $postData["wholesale_price"];

			$variables = $newRecord->prepareVariables($variables);
			$styleVerify = $newRecord->verifyVariables($variables);
			if(!count($styleVerify))//check for errors
				$styleid = $newRecord->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true); //insert if no errors

			//If everything ok then return uuid
			echo "OK".$styleid["uuid"];

		}//endif record found

	}//end method --importHeader-

}//end class --api_styleupdate--

if(!isset($noOutput) && isset($db)){

    $clean = new api_styleupdate($db);
    switch ($_POST["TX"]){
	case "HEADER":
		$clean->importHeader($_POST);
	break;
    }

}//end if
?>