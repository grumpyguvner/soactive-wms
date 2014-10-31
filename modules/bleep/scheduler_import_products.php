<?php

set_time_limit(0);

include_once("include/bleep_db.php");
include_once("../../include/tables.php");
//include_once("include/google_translate.php");

//uncomment for debug purposes
if(!class_exists("appError"))
	include_once("../../include/session.php");

class importBleepProducts{

	var $db;
	var $bleep_db;

	function importBleepProducts($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Products");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepProducts--

	//This method should import new and updated records
	//from the bleep database
	function importBleepProductRecords(){

//		$logError = new appError(0, "Error Details", "Bleep Import Products"); //also stops execution!

//		$productMessage = "Bleep Import Styles: Hello World (number)";
//		$log = new phpbmsLog($productMessage, "SCHEDULER");

		//////////////////////////////////////////////////////////////////////////////////////////
		//                                                                                      //
		// This import uses the bleep last changed field from the styles record to identify     //
		// changes that have been changed!!                                                     //
		//                                                                                      //
		// For records that have changed it creates/updates the product records as necessary    //
		// and updates the stock ..                                                             //
		//                                                                                      //
		//////////////////////////////////////////////////////////////////////////////////////////

		//and lets set a throttle
		$throttle = "0"; //100 should be sufficient to allow for colour/size combos??
                if(defined("BLEEP_PRODUCTTHROTTLE"))
                    $throttle = BLEEP_PRODUCTTHROTTLE;
$log = new phpbmsLog("fix throttle on product import", "TODO");
//currently if throttle is set to limit the records then repeats the same records
//if 1 style has more product records than the throttle ...

		$source_statement = "SELECT `styles`.`uuid` AS `styleid`,
					    `stylestosizes`.`sizeid` AS `sizeid`,
					    `stylestocolours`.`colourid` AS `colourid`,
					    CONCAT(`styles`.`stylenumber`, `stylestosizes`.`bleepid`, `stylestocolours`.`bleepid`) AS `bleepid`
				     FROM `styles`
					    JOIN `stylestocolours` 
					      ON `styles`.`uuid` = `stylestocolours`.`styleid`
					    JOIN `stylestosizes` 
					      ON `styles`.`uuid` = `stylestosizes`.`styleid`
				     WHERE (`styles`.`bleep_lastchanged`>`styles`.`bleep_lastproduct`)
				     ORDER BY `styles`.`bleep_lastchanged`";
		if(!$throttle=="0")
			$source_statement .= " LIMIT ".$throttle;

		$source_result = $this->db->query($source_statement);
//$log = new phpbmsLog($source_statement, "DEBUG PRODUCT");

		if(!$source_result){
			$error= new appError(0,"Could Not Retrieve Styles/Colours/Sizes from database","Bleep Import Products");
		}

//$log = new phpbmsLog("step 4", "DEBUG PRODUCT");
		while($source_record=$this->db->fetchArray($source_result)){


			//We need to make some basic overides to the bleep record
//			$source_record["id"] = str_pad($source_record["id"], 4, "0", STR_PAD_LEFT);

			$productMessage = $source_record["styleid"]." ";
//			$log = new phpbmsLog($productMessage, "BLEEP_PRODUCT");
$log = new phpbmsLog("importing products for style:".$source_record["styleid"]." - removing this causes an ERROR!", "TODO");

			//Before we start lets retrieve valuse for existing records
//			$mycolours = $this->generateColourString($source_record["id"]);
//			$updatecolours = false;
                        $styleid = substr($source_record["bleepid"],0,4);
                        $sizeid = substr($source_record["bleepid"],4,3);
                        $colourid = substr($source_record["bleepid"],7,4);

			//find the existing record (if one exists)
			$productstatement="SELECT uuid 
					  FROM products
					 WHERE styleid='".$source_record["styleid"]."'
					   AND colourid='".$source_record["colourid"]."'
					   AND sizeid='".$source_record["sizeid"]."'";
			$productresult = $this->db->query($productstatement);
			if(!$productresult){
				$error= new appError(0,"Could Not Retrieve Products","Bleep Import Products");
			}

//$log = new phpbmsLog("style 6", "DEBUG PRODUCT");
			$productfound = false;
			while($productrecord=$this->db->fetchArray($productresult)){
				$productfound = true;
				$productuuid = $productrecord["uuid"];
				$productstock = $this->getBleepStock(strval($source_record["bleepid"]), $productuuid);
				$productunitcost = $this->getUnitCost(strval($styleid), strval($colourid), strval($sizeid));
				$productseason = $this->getSeason(strval($styleid), strval($colourid), strval($sizeid));

				$products = new phpbmsTable($this->db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
				$productrecord = $products->getRecord($productrecord["uuid"],true);
				$productVars = array();

				$productChanged = false;

				foreach($productrecord as $productname => $productfield){

					switch($productname){

						//the following variables may change
						case "bleepid":
							$productVars[$productname] = $source_record["bleepid"];
							if($productVars[$productname]!=$productrecord[$productname]){
                                                            $productMessage .= "bleepid ";
                                                            $productChanged = true;
                                                        }
							break;

						case "bleep_stock":
							$productVars[$productname] = $productstock;
							if($productVars[$productname]!=$productrecord[$productname]){
                                                            $productMessage .= "stock ";
                                                            $productChanged = true;
                                                        }
							break;

						case "unitcost":
							$productVars[$productname] = $productunitcost;
							if($productVars[$productname]!=$productrecord[$productname]){
                                                            $productMessage .= "unit cost ";
                                                            $productChanged = true;
                                                        }
							break;

						case "season":
							$productVars[$productname] = $productseason;
							if(strcmp($productVars[$productname],$productrecord[$productname])){
								$productMessage .= "season ";
								$productChanged = true;
							}
							break;


						default://copy all remaining fields over
							$productVars[$productname] = $productfield;
							break;

					}//end switch

				}//endforeach

//$log = new phpbmsLog("style 7", "DEBUG PRODUCT");
				$productVars = $products->prepareVariables($productVars);
				$productVerify = $products->verifyVariables($productVars);
				if(!count($productVerify)){//check for errors
					if($productChanged){
						$productMessage .= " - Updated (".$productrecord["uuid"].") ";
						$log = new phpbmsLog($productMessage, "BLEEP_PRODUCT");
						$products->updateRecord($productVars,BLEEP_IMPORTUSER,true);

					} else {
						$productMessage .= " - Unchanged (".$productrecord["uuid"].") ";
//						$log = new phpbmsLog($productMessage, "BLEEP_PRODUCT");
					}
				}//insert if no errors

			}//end while

//$log = new phpbmsLog("style 8", "DEBUG PRODUCT");
			if(!$productfound){
				//couldn't find an existing record so create one
				$productMessage .= " - Inserting new record ";
				$log = new phpbmsLog($productMessage, "BLEEP_PRODUCT");
				$productChanged = true; //set the changed flag so that bleep_lastchanged gets set!

				//now we need to create product
				$products = new phpbmsTable($this->db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
				$productrecord = $products->getDefaults();

				$productVars = array();
				//load values from the bleep record:
				if($source_record["styleid"]) $productVars["styleid"] = $source_record["styleid"];
				if($source_record["colourid"]) $productVars["colourid"] = $source_record["colourid"];
				if($source_record["sizeid"]) $productVars["sizeid"] = $source_record["sizeid"];
				if($source_record["bleepid"]) $productVars["bleepid"] = $source_record["bleepid"];


				//load other default values:
//				$productVars["type"] = "Inventory";
//				$productVars["bleep_stock"] = $this->getBleepStock(strval($source_record["bleepid"]));
				$productVars["bleep_stock"] = 0; // we can only use getBleepStock with existing product record
				$productVars["unitcost"] = $this->getUnitCost(strval($styleid), strval($colourid), strval($sizeid));
				$productVars["season"] = $this->getSeason(strval($styleid), strval($colourid), strval($sizeid));

				$productVars = $products->prepareVariables($productVars);
				$productVerify = $products->verifyVariables($productVars);
				if(!count($productVerify))//check for errors
					$productrecord = $products->insertRecord($productVars,BLEEP_IMPORTUSER,false,false,true);//insert if no errors
				$productuuid = $productrecord["uuid"];

                                //NOW update the bleep_stock
				$productrecord["bleep_stock"] = $this->getBleepStock(strval($source_record["bleepid"]), $productuuid);
                                $productstatement="UPDATE products
                                                    SET bleep_stock=".$productrecord["bleep_stock"];
                                $productstatement.=" WHERE uuid='".$productuuid."'";
                                $productresult = $this->db->query($productstatement);
                                if(!$productresult){
                                        $error= new appError(0,"Could Not Update Bleep Stock","Bleep Import Products");
                                }
                                ;
			}//endif record found

			//update the bleep_lastimport date so that we know it has been done (even if nothing changed!)
			$productstatement="UPDATE products 
					  SET bleep_lastimport=NOW()";
			// We cant use last modified because it has changed simply by updating bleep_lastimport!
			if($productChanged) $productstatement.=", bleep_lastchanged=NOW()";
			$productstatement.=" WHERE uuid='".$productuuid."'";
			$productresult = $this->db->query($productstatement);
			if(!$productresult){
				$error= new appError(0,"Could Not Update Import Date","Bleep Import Products");
			}

			//update the bleep_lastproduct date so that we know it has been done (even if nothing changed!)
			// we need to update it with the oldest date, this does mean that if not all records processed
			//for a style then they will be repeated, which will cause a problem if the throttle is set!
//$log = new phpbmsLog("calculating last product date", "DEBUG PRODUCT");
                        $lastproductdate = $this->getLastProductDate($source_record["styleid"]);
//$log = new phpbmsLog("updating style record", "DEBUG PRODUCT");
                        if (!$lastproductdate){
                            //if we couldn't find a valid date, for example no active products
                            //then set to current date & time
                            $stylestatement="UPDATE styles
                                            SET bleep_lastproduct=NOW()
                                            WHERE uuid='".$source_record["styleid"]."'";
                        }else{
                            //update style with relevant date
                            $stylestatement="UPDATE styles
                                            SET bleep_lastproduct=('".$lastproductdate."')
                                            WHERE uuid='".$source_record["styleid"]."'";
                        }
//$log = new phpbmsLog($stylestatement, "DEBUG PRODUCT");
			$styleresult = $this->db->query($stylestatement);
			if(!$styleresult){
				$error= new appError(0,"Could Not Update Product Import Date","Bleep Import Products");
			}

		}//end while loop


$log = new phpbmsLog("deactivating unavailable records", "DEBUG PRODUCT");
		//set unavailable records to inactive (do not delete in case of previous orders)
		$deletestatement="UPDATE `products` 
					LEFT JOIN `stylestocolours`
					   ON (`products`.`styleid` = `stylestocolours`.`styleid`
					      AND `products`.`colourid` = `stylestocolours`.`colourid`)
					LEFT JOIN `stylestosizes`
					   ON (`products`.`styleid` = `stylestosizes`.`styleid`
					      AND `products`.`sizeid` = `stylestosizes`.`sizeid`)
					SET `products`.`inactive` = 1
					WHERE (`stylestocolours`.`id` IS NULL
					       OR `stylestosizes`.`id` IS NULL)";
		$productresult = $this->db->query($deletestatement);
		if(!$productresult)
			$error= new appError(-310,"Error 310.","Could Not Update Deleted Product Records");

$log = new phpbmsLog("updating style quantities", "DEBUG PRODUCT");
		//reset style quanities
		$updatestatement="UPDATE `styles`
					SET `styles`.`bleep_stock` =
					   (SELECT SUM(`bleep_stock`)
                                                   FROM `products`
                                                  WHERE `styles`.`uuid` = `products`.`styleid`);
                                           ";
		$updateresult = $this->db->query($updatestatement);
		if(!$updateresult)
			$error= new appError(-310,"Error 310.","Could Not Update Style Quantities");

	}//end method --importBleepProducts-

	function getLocationID($bleepid){
		//
		//find and return the existing location uuid (if one exists)
		//
		$mylocationid = "";

		$productstatement="SELECT uuid FROM locations WHERE bleepid='".$bleepid."'";
		$productresult = $this->db->query($productstatement);

		if(!$productresult){
			$error= new appError(-310,"Error 310.","Could Not Retrieve locations");
			$mylocationid = false;

		} else {
			while($therecord=$this->db->fetchArray($productresult)){

				$mylocationid = $therecord["uuid"];

			}//end while

		}//endif queryresult
		//
		//we should now have a locationid
		//

        	return $mylocationid;

	}//end function getLocationID

	function getLastProductDate($mystyleid){
		//
		//find and return the existing location uuid (if one exists)
		//
		$myDate = false;

                //2010-11-03: Added inactive=0 so that we update with the oldest ACTIVE record, otherwise
                //            gets stuck on styles with old records!
		$querystatement="SELECT bleep_lastimport FROM products
					 WHERE (styleid='".$mystyleid."'
					   AND inactive=0)
					 ORDER BY bleep_lastimport LIMIT 1";
		$queryresult = $this->db->query($querystatement);

		if(!$queryresult){
			$error= new appError(-310,"Error 310.","Could not retrieve date from product records");
			$myDate = false;

		} else {
			while($therecord=$this->db->fetchArray($queryresult)){

				$myDate = $therecord["bleep_lastimport"];

			}//end while

		}//endif queryresult
		//
		//we should now have a valid date
		//

        	return $myDate;

	}//end function getLastProductdate

    function getBleepStock($bleepid = "0", $productuuid = ""){
	//
	//find and return the existing stock from bleep
	//
	$mystock = 0;

	$bnstock = $this->getLocationStock("BRIGHTON", $productuuid, $bleepid);
        if (!$bnstock)
            $mystock=$mystock+$bnstock;

        $cgstock = $this->getLocationStock("CGARDEN", $productuuid, $bleepid);
        if (!$cgstock)
            $mystock=$mystock+$cgstock;

        $bhstock = $this->getLocationStock("WEBSTORE", $productuuid, $bleepid);
        if (!$bhstock)
            $mystock=$mystock+$bhstock;

        $whstock = $this->getLocationStock("WHSE", $productuuid, $bleepid);
        if (!$whstock)
            $mystock=$mystock+$whstock;

        return $mystock;

    }//end function getBleepStock

    function getLocationStock($location, $productuuid, $bleepid = "0"){
	//
	//find and return the existing stock from bleep
	//
	$mystock = 0;

	$querystatement="SELECT `current_stock` AS `bleep_stock` FROM ".$location." WHERE `product_id`='".$bleepid."'";
	$queryresult = $this->bleep_db->query($querystatement);
//$log = new phpbmsLog($querystatement, "DEBUG LOCATION STOCK");

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Product Stock Level");
		return false;

	} else {
		while($therecord=$this->bleep_db->fetchArray($queryresult)){

			$mystock = $therecord["bleep_stock"];

		}//end while

	}//endif queryresult
//$log = new phpbmsLog("STOCK=".$mystock, "DEBUG LOCATION STOCK");

        //now we need to create location stock
        $locationproducts = new phpbmsTable($this->db,"tbld:cf6413ca-d3db-eeea-4841-e2490ef50ef0");
        $locationrecord = $locationproducts->getDefaults();

        $myVars = array();
        //load values from the bleep record:
        $myVars["locationid"] = $this->getLocationID($location);
        $myVars["productid"] = $productuuid;
        $myVars["quantity"] = $mystock;


			//find the existing record (if one exists)
			$checkstatement="SELECT uuid 
					  FROM productsbylocation
					 WHERE productid='".$myVars["productid"]."'
					   AND locationid='".$myVars["locationid"]."'";
			$checkresult = $this->db->query($checkstatement);
			if (count($checkresult)>0){
				$deletestatement="DELETE 
					  FROM productsbylocation
					 WHERE productid='".$myVars["productid"]."'
					   AND locationid='".$myVars["locationid"]."'";
					$deleteresult = $this->db->query($deletestatement);
			}


        $myVars = $locationproducts->prepareVariables($myVars);
        $myVerify = $locationproducts->verifyVariables($myVars);
        if(!count($myVerify))//check for errors
            if ($mystock>0)
                $locationrecord = $locationproducts->insertRecord($myVars,BLEEP_IMPORTUSER,false,false,true);//insert if no errors
        $locationuuid = $locationrecord["uuid"];

        if ($locationuuid!="")
            return $mystock;

        return false;

    }//end function getLocationStock

    function getUnitCost($style = "0", $colour = "0", $size = "0"){
	//
	//find and return the unit cost from bleep
	//
	$mycost = 0;

	$querystatement="SELECT `cost_price` AS `unitcost` FROM ECWAREHOUSEDB WHERE `style`=".$style." AND `colour`=".$colour." AND `size`=".$size.";";
	$queryresult = $this->bleep_db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Product Unit Cost");
		$mycost = false;

	} else {
		while($therecord=$this->bleep_db->fetchArray($queryresult)){

			$mycost = $therecord["unitcost"];
			return $mycost;

		}//end while

	}//endif queryresult

        return 0;

    }//end function getUnitCost

    function getSeason($style = "0", $colour = "0", $size = "0"){
	//
	//find and return the unit cost from bleep
	//
	$myseason = "";

	$querystatement="SELECT `season` AS `season` FROM ECWAREHOUSEDB WHERE `style`=".$style." AND `colour`=".$colour." AND `size`=".$size.";";
	$queryresult = $this->bleep_db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Product Unit Cost");
		$myseason = false;

	} else {
		while($therecord=$this->bleep_db->fetchArray($queryresult)){

			$myseason = $therecord["season"];
			return $myseason;

		}//end while

	}//endif queryresult

        return 0;

    }//end function getSeason

}//end class --importBleepProducts--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepProducts($db);
    $clean->importBleepProductRecords();

}//end if
?>
