<?php
/////////////////////////////////////////////
//   IMPORT STOCK UPDATES TO ACTIVEWMS     //
/////////////////////////////////////////////
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");
include("include/tables.php");

//$now = gmdate('Y-m-d H:i', strtotime('now'));

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class api_stockupdate{

	function api_stockupdate($db){
		$this->db = $db;
	}//end method --api_stockupdate--

	//This method should import new and updated records
	//from the edi database
	function importHeader($postData){

//          $logError = new appError(0, "Error Details", "EDI STOCK UPDATE"); //also stops execution!

//          $message = "EDI STOCK UPDATE: Hello World (number)";
//          $log = new phpbmsLog($message, "EDI STOCK UPDATE", NULL, $this->db);

            $message = "New stock quantity override [".$postData["quantity_remaining"]."] received from ".$postData["location"]." - ".$postData["basket"];
            $message .=" for ".$postData["plu"];
            $message .=" on ".$postData["modifieddate"];

            $log = new phpbmsLog($message, "EDI STOCK UPDATE", NULL, $this->db);

            $this->updateLocationStock($postData["location"], $postData["plu"], $postData["quantity_remaining"], Â$postData["basket"]);

	}//end method --importHeader-

    function updateLocationStock($location, $plu, $newQuantity = 0, $newBasket = "") {
	//
	//update the existing stock quantity
	//

        //ensure product and location exist
        //by retrieving uuid's:
        $myLocationid = $this->getLocationID($location);
        if(!$myLocationid){
            $log = new phpbmsLog("Location [".$location."] not recognised", "EDI STOCK UPDATE", NULL, $this->db);
            return false;
        }
        $myProductid = $this->getProductID($plu);
        if(!$myProductid){
            $log = new phpbmsLog("PLU [".$plu."] not recognised", "EDI STOCK UPDATE", NULL, $this->db);
            return false;
        }
        $myStyleid = $this->getStyleID($plu);
        if(!$myStyleid){
            $log = new phpbmsLog("Style for PLU [".$plu."] not found", "EDI STOCK UPDATE", NULL, $this->db);
//            return false;
        }

        //find the existing stock record (if one exists)
//$log = new phpbmsLog("step 3", "EDI STOCK UPDATE", NULL, $this->db);
        $message = "";
        $querystatement='SELECT `uuid` FROM `productsbylocation`
                          WHERE `locationid`=\''.$myLocationid.'\'
                            AND `productid`=\''.$myProductid.'\'';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Retrieve Stock Record","EDI STOCK UPDATE");
        }

        $found = false;
        while($therecord=$this->db->fetchArray($queryresult)){
                $found = true;
//$log = new phpbmsLog("step 3a", "EDI STOCK UPDATE", NULL, $this->db);

                $stock = new phpbmsTable($this->db,"tbld:cf6413ca-d3db-eeea-4841-e2490ef50ef0");
                $therecord = $stock->getRecord($therecord["uuid"],true);
                $variables = array();

                $changed = false;
                foreach($therecord as $name => $field){

                        switch($name){

                                //the following variables may change
                                case "quantity":
                                        $variables[$name] = $newQuantity;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                default://copy all remaining fields over
                                        $variables[$name] = $field;
                                        break;

                        }//end switch

                }//endforeach

                $variables = $stock->prepareVariables($variables);
                $stockVerify = $stock->verifyVariables($variables);
                if(!count($stockVerify)){//check for errors
                        if($changed){
                        $message .= " - Updated (".$therecord["uuid"].") ";
                        $log = new phpbmsLog($message, "EDI STOCK UPDATE", NULL, $this->db);

                                $stock->updateRecord($variables,BLEEP_IMPORTUSER,true);
                        } else {
                                $message .= " - Unchanged (".$therecord["uuid"].") ";
                        	$log = new phpbmsLog($message, "EDI STOCK UPDATE", NULL, $this->db);
                        }

                        //If everything ok then return uuid
                        echo "OK".$therecord["uuid"];
                }//insert if no errors

        }//end while

        if(!$found){
$log = new phpbmsLog("step 3b", "EDI STOCK UPDATE", NULL, $this->db);
                //couldn't find an existing record so create one
                $message .= " - Inserting new record ";
                $log = new phpbmsLog($message, "EDI STOCK UPDATE", NULL, $this->db);

                //now we need to create a new stock record
                $stock = new phpbmsTable($this->db,"tbld:cf6413ca-d3db-eeea-4841-e2490ef50ef0");
                $therecord = $stock->getDefaults();

                $variables = array();

                //Set some defaults
                $variables["locationid"] = $myLocationid;
                $variables["productid"] = $myProductid;
                $variables["quantity"] = $newQuantity;

                $variables = $stock->prepareVariables($variables);
                $stockVerify = $stock->verifyVariables($variables);
                if(!count($stockVerify))//check for errors
                        $stockid = $stock->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true); //insert if no errors
                //If everything ok then return uuid
                if($stockid)
                    echo "OK".$stockid;

        }//endif record found

        $log = new phpbmsLog("TODO: Move quantity updates to database triggers", "EDI STOCK UPDATE", NULL, $this->db);
        //Update the stock summary levels
        // easier to do for all products
        $querystatement='UPDATE `products` SET
                            `bleep_webstore` = (SELECT SUM(`quantity`) FROM `productsbylocation` WHERE `productid` = `products`.`uuid` AND `locationid`=\'locn:67b54589-183e-cd28-a38e-c9650c12e19b\'),
                            `bleep_brighton` = (SELECT SUM(`quantity`) FROM `productsbylocation` WHERE `productid` = `products`.`uuid` AND `locationid`=\'locn:fd162662-bb70-2363-de84-2c70afc49d4f\'),
                            `bleep_cgarden` = (SELECT SUM(`quantity`) FROM `productsbylocation` WHERE `productid` = `products`.`uuid` AND `locationid`=\'locn:5a593188-5656-00b5-82ef-d34d8ff08293\'),
                            `bleep_whse` = (SELECT SUM(`quantity`) FROM `productsbylocation` WHERE `productid` = `products`.`uuid` AND `locationid`=\'locn:f11967a6-deb0-8db0-bda5-560873ef6cd5\')
                          `location` ="'.$newBasket.'"
                          WHERE `uuid` ="'.$myProductid.'";';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Update Quantities at Product Level","EDI STOCK UPDATE");
        }
        //Update available quantity
        $querystatement='UPDATE `products` p JOIN `styles` s ON (p.`styleid`=s.`uuid`)
                            SET p.`available_stock`=(p.`bleep_webstore`+p.`bleep_whse`+(p.`bleep_brighton`*s.`inc_brighton`))
                          WHERE p.`uuid` ="'.$myProductid.'";';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Update Quantities at Product Level","EDI STOCK UPDATE");
        }
        //Update available quantity at Style Level
        $querystatement='UPDATE styles SET available_stock = (SELECT SUM(available_stock) FROM products WHERE products.styleid = styles.uuid)
                          WHERE `uuid` ="'.$myStyleid.'";';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Update Quantities at Product Level","EDI STOCK UPDATE");
        }

    }//end function updateLocationStock

    function getLocationID($bleepid){
	//
	//find and return the existing location uuid (if one exists)
	//
        $mylocationid = false;

	$querystatement="SELECT uuid FROM locations WHERE bleepid='".$bleepid."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve locations");

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mylocationid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a locationid
	//

        return $mylocationid;

    }//end function getLocationID

    function getProductID($bleepid){
	//
	//find and return the existing product uuid (if one exists)
	//
        $myproductid = false;

	$querystatement="SELECT uuid FROM products WHERE bleepid='".$bleepid."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve products");

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$myproductid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a productid
	//

        return $myproductid;

    }//end function getProductID

    function getStyleID($bleepid){
	//
	//find and return the existing style uuid (if one exists)
        //given the PLU
	//
        $mystyleid = false;

	$querystatement="SELECT styles.uuid
                              FROM products
                         LEFT JOIN styles
                                ON (products.styleid = styles.uuid)
                          WHERE products.bleepid='".$bleepid."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve styles");

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mystyleid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a productid
	//

        return $mystyleid;

    }//end function getProductID

}//end class --api_stockupdate--

if(!isset($noOutput) && isset($db)){

    $clean = new api_stockupdate($db);
    switch ($_POST["TX"]){
	case "HEADER":
		$clean->importHeader($_POST);
	break;
//	case "DETAIL":
//		$clean->importDetail($_POST);
//	break;
//	case "TOTALS":
//		$clean->importTotals($_POST);
//	break;
    }

}//end if
?>
