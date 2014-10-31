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

class api_saleupdate{

    function api_saleupdate($db){
            $this->db = $db;
    }//end method --api_saleupdate--

    //This method should import new and updated records
    //from the edi database
    function importSale($postData){

//          $logError = new appError(0, "Error Details", "EDI NEW SALE"); //also stops execution!

//          $message = "EDI SALE: Hello World (number)";
//          $log = new phpbmsLog($message, "EDI NEW SALE", NULL, $this->db);

        $message = "New sale received from ".$postData["location"];
        $message .=" for ".$postData["plu"];
        $message .=" on ".$postData["sale_date"];

        $log = new phpbmsLog($message, "EDI NEW SALE", NULL, $this->db);

        $this->updateLocationSale($postData["location"], $postData["plu"], $postData["sale_date"], $postData["sale_id"], $postData["line"], $postData["quantity"], $postData["unitcost"], $postData["unitprice"], $postData["discount"], $postData["discount_type"]);

    }//end method --importSale-

    function updateLocationSale($location, $plu, $sale_date, $sale_id, $line, $quantitySold = 0, $unitCost = 0, $unitPrice = 0, $discount = 0, $discountType = ""){
	//
	//update the existing sale quantity
	//

        //ensure product and location exist
        //by retrieving uuid's:
        $myLocationid = $this->getLocationID($location);
        if(!$myLocationid){
            $log = new phpbmsLog("Location [".$location."] not recognised", "EDI NEW SALE", NULL, $this->db);
            return false;
        }
        $myProductid = $this->getProductID($plu);
        if(!$myProductid){
            $log = new phpbmsLog("PLU [".$plu."] not recognised", "EDI NEW SALE", NULL, $this->db);
            return false;
        }
        $myStyleid = $this->getStyleID($plu);
        if(!$myStyleid){
            $log = new phpbmsLog("Style for PLU [".$plu."] not found", "EDI NEW SALE", NULL, $this->db);
//            return false;
        }
        $mySaleDate = $sale_date;
        $mySaleid = $sale_id;
        $myLine = $line;

        //find the existing sale record (if one exists)
        $message = "";
        $querystatement='SELECT `uuid` FROM `productsalesbylocation`
                          WHERE `locationid`=\''.$myLocationid.'\'
                            AND `sale_id`=\''.$mySaleid.'\'
                            AND `line`=\''.$myLine.'\'';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Retrieve Product Sale Record","EDI NEW SALE");
        }

        $found = false;
        while($therecord=$this->db->fetchArray($queryresult)){
                $found = true;
//$log = new phpbmsLog("step 3a", "EDI NEW SALE", NULL, $this->db);

                $sale = new phpbmsTable($this->db,"tbld:537a027a-edc7-11e0-8bec-001d0923519e");
                $therecord = $sale->getRecord($therecord["uuid"],true);
                $variables = array();

                $changed = false;
                foreach($therecord as $name => $field){

                        switch($name){

                                //the following variables may change
                                case "quantity":
                                        $variables[$name] = $quantitySold;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "unitcost":
                                        $variables[$name] = $unitCost;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "unitprice":
                                        $variables[$name] = $unitPrice;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "discount":
                                        $variables[$name] = $discount;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "discount_type":
                                        $variables[$name] = $discountType;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "plu":
                                        $variables[$name] = $plu;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "productid":
                                        $variables[$name] = $myProductid;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "styleid":
                                        $variables[$name] = $myStyleid;
                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                        break;

                                case "sale_date": //date objects
                                        $variables[$name] = formatFromSQLDatetime($sale_date);
                                        if(!strtotime($variables[$name])==strtotime($therecord[$name])) $changed = true;
                                        break;

                                default://copy all remaining fields over
                                        $variables[$name] = $field;
                                        break;

                        }//end switch

                }//endforeach

                $variables = $sale->prepareVariables($variables);
                $saleVerify = $sale->verifyVariables($variables);
                if(!count($saleVerify)){//check for errors
                        if($changed){
                        $message .= " - Updated (".$therecord["uuid"].") ";
                        $log = new phpbmsLog($message, "EDI NEW SALE", NULL, $this->db);

                                $sale->updateRecord($variables,BLEEP_IMPORTUSER,true);
                        } else {
                                $message .= " - Unchanged (".$therecord["uuid"].") ";
                        	$log = new phpbmsLog($message, "EDI NEW SALE", NULL, $this->db);
                        }

                        //If everything ok then return uuid
                        echo "OK".$therecord["uuid"];
                }//insert if no errors

        }//end while

        if(!$found){
                //couldn't find an existing record so create one
                $message .= " - Inserting new record ";
                $log = new phpbmsLog($message, "EDI NEW SALE", NULL, $this->db);

                //now we need to create a new sale record
                $sale = new phpbmsTable($this->db,"tbld:537a027a-edc7-11e0-8bec-001d0923519e");
                $therecord = $sale->getDefaults();

                $variables = array();

                //Set some defaults
                $variables["sale_date"] = formatFromSQLDatetime($sale_date);
                $variables["sale_id"] = $mySaleid;
                $variables["line"] = $myLine;
                $variables["locationid"] = $myLocationid;
                $variables["plu"] = $plu;
                $variables["productid"] = $myProductid;
                $variables["styleid"] = $myStyleid;
                $variables["quantity_sold"] = $quantitySold;
                $variables["unitcost"] = $unitCost;
                $variables["unitprice"] = $unitPrice;
                $variables["discount"] = $discount;
                $variables["discount_type"] = $discountType;

                $variables = $sale->prepareVariables($variables);
                $saleVerify = $sale->verifyVariables($variables);
                if(!count($saleVerify))//check for errors
                        $saleid = $sale->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true); //insert if no errors
                //If everything ok then return uuid
                if($saleid)
                    echo "OK".$saleid;

        }//endif record found


    }//end function updateLocationSale

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

}//end class --api_saleupdate--

if(!isset($noOutput) && isset($db)){

    $clean = new api_saleupdate($db);
    switch ($_POST["TX"]){
	case "SALE":
		$clean->importSale($_POST);
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