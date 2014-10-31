<?php
/////////////////////////////////////////////
//   UPDATE ORDER STATUS                   //
/////////////////////////////////////////////
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");
include("include/order.php");
include("include/orderitem.php");

//$now = gmdate('Y-m-d H:i', strtotime('now'));

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class updateEdiOrderStatus{

	function updateEdiOrderStatus($db){
		$this->db = $db;
	}//end method --updateEdiOrderStatus--

	//This method should import new and updated records
	//from the edi database
	function changeStatus($postData){

//		$logError = new appError(0, "Error Details", "EDI UPDATE ORDER STATUS"); //also stops execution!

//		$message = "EDI NEW ORDER: Hello World (number)";
//		$log = new phpbmsLog($message, "EDI UPDATE ORDER STATUS", NULL, $this->db);

		$message = "New order status received for ".$postData["leadsource"];
		$message .=" ref ".$postData["webconfirmationno"];
		$message .=" [".$postData["newstatus"]."]";
	
		$log = new phpbmsLog($message, "EDI UPDATE ORDER STATUS", NULL, $this->db);

		//find the existing record (if one exists)
		$querystatement='SELECT `uuid` FROM `orders` 
                                  WHERE `leadsource`=\''.$postData["leadsource"].'\'
                                    AND `webconfirmationno`=\''.$postData["webconfirmationno"].'\'';
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult){
			$error= new appError(0,"Could Not Retrieve Order","EDI UPDATE ORDER STATUS");
		}

		$found = false;
		while($therecord=$this->db->fetchArray($queryresult)){
			$found = true;

			$order = new order($this->db);
			$therecord = $order->getRecord($therecord["uuid"],true);
			$variables = array();

			$changed = false;
			foreach($therecord as $name => $field){

				switch($name){
					case "uuid":
						$variables[$name] = $field;
						break;

					//the following variables may change
					case "statusid":
						$variables[$name] = $postData["newstatus"];
						if(strcmp($variables[$name],$therecord[$name])) $changed = true;
						break;
//					default://copy all remaining fields over
//						$variables[$name] = $field;
//						break;

				}//end switch

			}//endforeach

			$variables = $order->prepareVariables($variables);
			$orderVerify = $order->verifyVariables($variables);
			if(!count($orderVerify)){//check for errors
				if($changed){
				$message .= " - Updated (".$therecord["uuid"].") ";
				$log = new phpbmsLog($message, "EDI UPDATE ORDER STATUS", NULL, $this->db);

					$order->updateRecord($variables,BLEEP_IMPORTUSER,true);
				} else {
					$message .= " - Unchanged (".$therecord["uuid"].") ";
				//	$log = new phpbmsLog($message, "EDI UPDATE ORDER STATUS", NULL, $this->db);
				}
	
				//If everything ok then return uuid
				echo "OK".$therecord["uuid"];
			}//insert if no errors

		}//end while

		if(!$found){
			//couldn't find an existing record so create one
			$message .= " - UNABLE TO FIND ORDER ...";
			$log = new phpbmsLog($message, "EDI UPDATE ORDER STATUS", NULL, $this->db);

			//If everything ok then return uuid
			echo "ERROR: UNABLE TO FIND ORDER ...";

		}//endif record found

	}//end method --changeStatus-


}//end class --updateEdiOrderStatus--

if(!isset($noOutput) && isset($db)){

    $clean = new updateEdiOrderStatus($db);
    switch ($_POST["TX"]){
	case "UPDATE_STATUS":
		$clean->changeStatus($_POST);
	break;
    }

}//end if
?>