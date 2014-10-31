<?php
/////////////////////////////////////////////
//   IMPORT STYLE UPDATES TO ACTIVEWMS     //
/////////////////////////////////////////////
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");

//$now = gmdate('Y-m-d H:i', strtotime('now'));

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class updateEdiProduct{

	function updateEdiProduct($db){
		$this->db = $db;
	}//end method --updateEdiProduct--

	//This method should import only updated records
	// now provision is made for creating new products
        // they must already exist in activewms
	function importHeader($postData){

//		$logError = new appError(0, "Error Details", "EDI UPDATED STYLE"); //also stops execution!

//		$message = "EDI UPDATED STYLE: Hello World (number)";
//		$log = new phpbmsLog($message, "EDI UPDATED STYLE", NULL, $this->db);

		$message = "Style details received from ".$postData["source"];
		$message .=" ref ".$postData["stylenumber"];
		$message .=" updated ".$postData["date_upd"];
	
		//ensure that the style exists before continuing
		$querystatement='SELECT `uuid` FROM `styles`
                                  WHERE `stylenumber`=\''.$postData["stylenumber"].'\'';
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult){
			$error= new appError(0,"Could Not Retrieve Styles","EDI UPDATED STYLE");
		}

		$found = false;
		while($queryrecord=$this->db->fetchArray($queryresult)){
			$found = true;

			$style = new styles($this->db);
			$therecord = $style->getRecord($queryrecord["uuid"],true);
			$variables = array();

			$changed = false;

			$message .= " - Checking for updates ";
                        foreach($therecord as $name => $field){

                                switch($name){

                    $this->post_data["source"] = $_SERVER['SERVER_NAME'];
                    $this->post_data["stylenumber"] = $obj["styleid"];
                    $this->post_data["site"] = $obj["site"];
                    $this->post_data["stylename"] = $obj["stylename"];
                    $this->post_data["description"] = $obj["description"];
                    $this->post_data["keywords"] = $obj["keywords"];
                    $this->post_data["webdescription"] = $obj["webdescription"];
                    $this->post_data["ecotax"] = $obj["ecotax"];
                    $this->post_data["price"] = $obj["price"];
                    $this->post_data["reduction_price"] = $obj["reduction_price"];
                    $this->post_data["reduction_percent"] = $obj["reduction_percent"];
                    $this->post_data["wholesale_price"] = $obj["wholesale_price"];
                    $this->post_data["link_rewrite"] = $obj["link_rewrite"];
                    $this->post_data["meta_description"] = $obj["meta_description"];
                    $this->post_data["meta_title"] = $obj["meta_title"];
                    $this->post_data["available_now"] = $obj["available_now"];
                    $this->post_data["available_later"] = $obj["available_later"];
                    $this->post_data["date_upd"] = $obj["date_upd"];


                                        //the following variables may change
                                        case "stylename":
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

		}//end while

		if(!$found){
			//couldn't find a style record
			$message .= " - Unable to find existing style ";
		}//endif record found

                $log = new phpbmsLog($message, "EDI UPDATED STYLE", NULL, $this->db);
                //If everything ok then return uuid
                echo "OK".$therecord["uuid"];

	}//end method --importHeader-

}//end class --updateEdiProduct--

if(!isset($noOutput) && isset($db)){

    $clean = new updateEdiProduct($db);
    switch ($_POST["TX"]){
	case "PRODUCT":
		$clean->importHeader($_POST);
	break;
    }

}//end if
?>