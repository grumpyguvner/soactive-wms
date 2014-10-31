<?php
/////////////////////////////////////////////
//   IMPORT NEW ORDERS TO ACTIVEWMS        //
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

class importEdiOrder{

	function importEdiOrder($db){
		$this->db = $db;
	}//end method --importEdiOrder--

	//This method should import new and updated records
	//from the edi database
	function importHeader($postData){

//		$logError = new appError(0, "Error Details", "EDI NEW ORDER"); //also stops execution!

//		$message = "EDI NEW ORDER: Hello World (number)";
//		$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

		$message = "New order received from ".$postData["leadsource"];
		$message .=" ref ".$postData["webconfirmationno"];
		$message .=" order date ".$postData["orderdate"];
	
		$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

		//find the existing record (if one exists)
		$querystatement='SELECT `uuid` FROM `orders` 
                                  WHERE `leadsource`=\''.$postData["leadsource"].'\'
                                    AND `webconfirmationno`=\''.$postData["webconfirmationno"].'\'';
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult){
			$error= new appError(0,"Could Not Retrieve Orders","EDI NEW ORDER");
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

					//the following variables may change
					case "billtoname":
						//$variables["gender"] = $postData["gender"];
						$variables[$name] = 
						  $postData["firstname"]." ".$postData["lastname"];
						if(strcmp($variables[$name],$therecord[$name])) $changed = true;
						break;

					case "billtoemail":
					case "billtoaddress1":
					case "billtoaddress2":
					case "billtocity":
					case "billtostate":
					case "billtopostcode":
					case "billtocountry":
					case "shippingmethod":
					case "shiptoname":
					case "shiptoaddress1":
					case "shiptoaddress2":
					case "shiptocity":
					case "shiptostate":
					case "shiptopostcode":
					case "shiptocountry":
					case "shiptotelephone":
						$variables[$name] = $postData[$name];
						if(strcmp($variables[$name],$therecord[$name])) $changed = true;
						break;

					case "orderdate": //non-string objects
						$variables[$name] = formatFromSQLDatetime($postData["orderdate"]);
						if($variables[$name]!=$therecord[$name]) $changed = true;
						break;

					default://copy all remaining fields over
						$variables[$name] = $field;
						break;

				}//end switch

			}//endforeach

			$variables = $order->prepareVariables($variables);
			$orderVerify = $order->verifyVariables($variables);
			if(!count($orderVerify)){//check for errors
				if($changed){
				$message .= " - Updated (".$therecord["uuid"].") ";
				$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

					$order->updateRecord($variables,BLEEP_IMPORTUSER,true);
				} else {
					$message .= " - Unchanged (".$therecord["uuid"].") ";
				//	$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);
				}
	
				//If everything ok then return uuid
				echo "OK".$therecord["uuid"];
			}//insert if no errors

		}//end while

		if(!$found){
			//couldn't find an existing record so create one
			$message .= " - Inserting new record ";
			$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

			//now we need to create colour
			$order = new order($this->db);
			$therecord = $order->getDefaults();

			$variables = array();
			if($postData["leadsource"])
				$variables["leadsource"] = $postData["leadsource"];
			if($postData["webconfirmationno"])
				$variables["webconfirmationno"] = $postData["webconfirmationno"];
			if($postData["orderdate"])
				$variables["orderdate"] = formatFromSQLDatetime($postData["orderdate"]);
			if($postData["billtoemail"])
				$variables["billtoemail"] = $postData["billtoemail"];
			//if($postData["gender"])
			//	$variables["billtoname"] = $postData["lastname"];
			//TODO: Create a customer??
			if($postData["lastname"])
				$variables["billtoname"] = $postData["lastname"];
			if($postData["firstname"])
				$variables["billtoname"] = $postData["firstname"]." ".$variables["billtoname"];
			if($postData["billtoaddress1"])
				$variables["billtoaddress1"] = $postData["billtoaddress1"];
			if($postData["billtoaddress2"])
				$variables["billtoaddress2"] = $postData["billtoaddress2"];
			if($postData["billtocity"])
				$variables["billtocity"] = $postData["billtocity"];
			if($postData["billtostate"])
				$variables["billtostate"] = $postData["billtostate"];
			if($postData["billtopostcode"])
				$variables["billtopostcode"] = $postData["billtopostcode"];
			if($postData["billtocountry"])
				$variables["billtocountry"] = $postData["billtocountry"];
			if($postData["shippingmethod"])
				$variables["shippingmethod"] = $postData["shippingmethod"];
			if($postData["shiptoname"])
				$variables["shiptoname"] = $postData["shiptoname"];
			if($postData["shiptoaddress1"])
				$variables["shiptoaddress1"] = $postData["shiptoaddress1"];
			if($postData["shiptoaddress2"])
				$variables["shiptoaddress2"] = $postData["shiptoaddress2"];
			if($postData["shiptocity"])
				$variables["shiptocity"] = $postData["shiptocity"];
			if($postData["shiptostate"])
				$variables["shiptostate"] = $postData["shiptostate"];
			if($postData["shiptopostcode"])
				$variables["shiptopostcode"] = $postData["shiptopostcode"];
			if($postData["shiptocountry"])
				$variables["shiptocountry"] = $postData["shiptocountry"];
			if($postData["shiptotelephone"])
				$variables["shiptotelephone"] = $postData["shiptotelephone"];

			//Set some defaults
			$variables["weborder"] = true;

			$variables = $order->prepareVariables($variables);
			$orderVerify = $order->verifyVariables($variables);
			if(!count($orderVerify))//check for errors
				$orderid = $order->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true); //insert if no errors

			//If everything ok then return uuid
			echo "OK".$orderid["uuid"];

		}//endif record found

	}//end method --importHeader-

	//This method should import new and updated records
	//from the edi database
	function importDetail($postData){

		//
		//we can only add detail lines for an order which exists
$log = new phpbmsLog("DEBUG: CHECKING TO SEE IF ORDER EXISTS (".$postData["orderid"].")", "EDI NEW ORDER", NULL, $this->db);
		$order = new order($this->db);
		$order = $order->getRecord($postData["orderid"], true);
		//we can only continue if uuid is valid
		//if we dont do this we will add a new record!
		if($order["id"]===NULL){
			$error= new appError(0,"Invalid Order ID ".$postData["orderid"], "EDI NEW ORDER");
		}

$log = new phpbmsLog("TODO: NEED TO VALIDATE THAT ORDER CAN ACCEPT UPDATES", "EDI NEW ORDER", NULL, $this->db);

		$message = "New order detail received for ".$order["leadsource"];
		$message .=" ref ".$order["webconfirmationno"];
		$message .=" product ".$postData["upc"];
	
		$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

		//find the existing record (if one exists)
		$querystatement='SELECT `uuid` FROM `orderitems` 
                                  WHERE `orderid`=\''.$postData["orderid"].'\'
                                    AND `upc`=\''.$postData["upc"].'\'';
$log = new phpbmsLog("TODO: CAN WE HAVE MULTI LINE ORDER WITH SAME UPC?", "EDI NEW ORDER", NULL, $this->db);
		$queryresult = $this->db->query($querystatement);
		if(!$queryresult){
			$error= new appError(0,"Could Not Retrieve Order Items","EDI NEW ORDER");
		}

		$found = false;
		while($therecord=$this->db->fetchArray($queryresult)){
			$found = true;

			$oitem = new orderitem($this->db);
			$therecord = $oitem->getRecord($therecord["uuid"],true);
			$variables = array();

			$changed = false;
			foreach($therecord as $name => $field){

				switch($name){

					//the following variables may change
					case "brand": //string objects
					case "stylename":
					case "size":
					case "colour":
					case "unitpromocode":
						$variables[$name] = $postData[$name];
						if(strcmp($variables[$name],$therecord[$name])) $changed = true;
						break;

					case "quantity": //numeric objects
					case "unitcost":
					case "unitdiscount":
					case "unitprice":
					case "unitweight":
						$variables[$name] = $postData[$name];
						if($variables[$name]!=$therecord[$name]) $changed = true;
						break;

					default://copy all remaining fields over
						$variables[$name] = $field;
						break;

				}//end switch

			}//endforeach

			$variables = $oitem->prepareVariables($variables);
			$oitemVerify = $oitem->verifyVariables($variables);
			if(!count($oitemVerify)){//check for errors
				if($changed){
				$message .= " - Updated (".$therecord["uuid"].") ";
				$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

					$oitem->updateRecord($variables,BLEEP_IMPORTUSER,true);
				} else {
					$message .= " - Unchanged (".$therecord["uuid"].") ";
				//	$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);
				}
	
				//If everything ok then return uuid
				echo "OK".$therecord["uuid"];
			}//insert if no errors

		}//end while

		if(!$found){
			//couldn't find an existing record so create one
			$message .= " - Inserting new record ";
			$log = new phpbmsLog($message, "EDI NEW ORDER", NULL, $this->db);

			//now we need to create a new item
			$oitem = new orderitem($this->db);
			$therecord = $oitem->getDefaults();

			$variables = array();

			if($postData["orderid"])
				$variables["orderid"] = $postData["orderid"];
			if($postData["upc"])
				$variables["upc"] = $postData["upc"];
			if($postData["brand"])
				$variables["brand"] = $postData["brand"];
			if($postData["stylename"])
				$variables["stylename"] = $postData["stylename"];
			if($postData["size"])
				$variables["size"] = $postData["size"];
			if($postData["colour"])
				$variables["colour"] = $postData["colour"];
			if($postData["unitpromocode"])
				$variables["unitpromocode"] = $postData["unitpromocode"];
			if($postData["quantity"])
				$variables["quantity"] = $postData["quantity"];
			if($postData["unitcost"])
				$variables["unitcost"] = $postData["unitcost"];
			if($postData["unitdiscount"])
				$variables["unitdiscount"] = $postData["unitdiscount"];
			if($postData["unitprice"])
				$variables["unitprice"] = $postData["unitprice"];
			if($postData["unitweight"])
				$variables["unitweight"] = $postData["unitweight"];

			//Set some defaults
			$variables["weborder"] = true;

			$variables = $oitem->prepareVariables($variables);
			$oitemVerify = $oitem->verifyVariables($variables);
			if(!count($oitemVerify))//check for errors
				$oitemid = $oitem->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true); //insert if no errors

			//If everything ok then return uuid
			echo "OK".$oitemid["uuid"];

		}//endif record found

	}//end method --importDetail-

	//This method should import new and updated records
	//from the edi database
	function importTotals($postData){
		echo "ERROR";
	}//end method --importTotals-

}//end class --importEdiOrder--

if(!isset($noOutput) && isset($db)){

    $clean = new importEdiOrder($db);
    switch ($_POST["TX"]){
	case "HEADER":
		$clean->importHeader($_POST);
	break;
	case "DETAIL":
		$clean->importDetail($_POST);
	break;
	case "TOTALS":
		$clean->importTotals($_POST);
	break;
    }

}//end if
?>