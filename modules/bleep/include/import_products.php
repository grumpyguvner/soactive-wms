<?php

include_once("include/bleep_db.php");

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class importBleepProducts{

	var $db;
	var $bleep_db;

	function importBleepProducts($db){
		$this->db = $db;

		$this->bleep_db = new bleep_db();

		if($this->bleep_db==NULL){
			$error=new appError(0,"Unable to load Bleep Database","Bleep Import Styles");
		}

		$this->bleep_db->logError = false;
		$this->bleep_db->stopOnError = false;
		$this->bleep_db->setEncoding();
		$this->bleep_db->logError = true;

	}//end method --importBleepStyles--

	//This method should import new and updated records
	//from the bleep database
	function importBleepProductRecords($styleid){

//		$logError = new appError(0, "Error Details", "Bleep Import Products"); //also stops execution!

//		$message = "Bleep Import Products: Hello World (number)";
//		$log = new phpbmsLog($message, "SCHEDULER", NULL, $this->db);



	//
	//update the products table based on the current available sizes and colours by:
	// 1. create new records 
	// 2. update existing?
	// 3. set unavailable records to inactive (do not delete in case of previous orders)
	$success = false;

$log = new phpbmsLog("fingers crossed", "DEBUG STYLE PRODUCTS");
//	$product = new importBleepProducts($this->db);
$log = new phpbmsLog("here we go", "DEBUG STYLE PRODUCTS");
//	$product->importBleepProductrecords($styleid);
$log = new phpbmsLog("yahoo!", "DEBUG STYLE PRODUCTS");







		$selectstatement="SELECT `styles`.`uuid` AS `styleid`,
					 `stylestocolours`.`colourid` AS `colourid`,
					 `stylestosizes`.`sizeid` AS `sizeid`
				  FROM `styles`
				   JOIN `stylestocolours` 
				       ON `styles`.`uuid` = `stylestocolours`.`styleid`
				   JOIN `stylestosizes` 
				       ON `styles`.`uuid` = `stylestosizes`.`styleid`
				  WHERE `styles`.`uuid`='".$styleid."'";
		$selectresult = $this->db->query($selectstatement);

		if(!$selectresult)
			$error= new appError(-310,"Error 310.","Could Not Retrieve Style/Colour/Size Records");

		while($myrecord=$this->db->fetchArray($selectresult)){
$log = new phpbmsLog("=".$myrecord["styleid"].$myrecord["colourid"].$myrecord["sizeid"], "DEBUG STYLE PRODUCTS");
			//find the existing record (if one exists)

			$productstatement="SELECT uuid 
					  FROM products
					 WHERE styleid='".$myrecord["styleid"]"'
					   AND colourid='".$myrecord["colourid"]"'
					   AND sizeid='".$myrecord["sizeid"]"'";
			$productresult = $this->db->query($productstatement);
			if(!$productresult){
				$error= new appError(0,"Could Not Retrieve Products","Bleep Import Styles");
			}

//$log = new phpbmsLog("style 6", "DEBUG STYLE PRODUCTS");
			$productfound = false;
			while($productrecord=$this->db->fetchArray($productresult)){
				$productfound = true;
				$productuuid = $productrecord["uuid"];

				$products = new phpbmsTable($this->db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
				$productrecord = $products->getRecord($productrecord["uuid"],true);
				$productVars = array();

				$productChanged = false;
				foreach($productrecord as $productname => $productfield){

					switch($productname){

						//the following variables may change
//						case "bleepid":
//							$variables[$name] = $bleep_record["id"];
//							if($variables[$name]!=$therecord[$name]) $changed = true;
//							break;

//						case "stylename":
//							$productVars[$productname] = $myrecord["description"];
//							if(strcmp($productVars[$productname],$productrecord[$productname])){
//								$productMessage .= "name ";
//								$productChanged = true;
//							}
//							break;


						default://copy all remaining fields over
							$productVars[$productname] = $productfield;
							break;

					}//end switch

				}//endforeach

//$log = new phpbmsLog("style 7", "DEBUG STYLE PRODUCTS");
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

//$log = new phpbmsLog("style 8", "DEBUG STYLE PRODUCTS");
			if(!$productfound){
				//couldn't find an existing record so create one
				$productMessage .= " - Inserting new record ";
				$log = new phpbmsLog($productMessage, "BLEEP_PRODUCT");

				//now we need to create product
				$products = new phpbmsTable($this->db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
				$productrecord = $products->getDefaults();

				$productVars = array();
				//load values from the bleep record:
//				if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
				if($myrecord["styleid"]) $productVars["styleid"] = $myrecord["styleid"];
				if($myrecord["colourid"]) $productVars["colourid"] = $myrecord["colourid"];
				if($myrecord["sizeid"]) $productVars["sizeid"] = $myrecord["sizeid"];


				//load other default values:
//				$productVars["type"] = "Inventory";

				$productVars = $products->prepareVariables($productVars);
				$productVerify = $products->verifyVariables($productVars);
				if(!count($productVerify))//check for errors
					$productrecord = $products->insertRecord($productVars,BLEEP_IMPORTUSER,false,false,true);//insert if no errors
				$productuuid = $productrecord["uuid"];
			}//endif record found


		}//end while











	// 3. set unavailable records to inactive (do not delete in case of previous orders)

	$deletestatement="UPDATE `products` 
				LEFT JOIN `stylestocolours`
				   ON (`products`.`styleid` = `stylestocolours`.`styleid`
				      AND `products`.`colourid` = `stylestocolours`.`colourid`)
				LEFT JOIN `stylestosizes`
				   ON (`products`.`styleid` = `stylestosizes`.`styleid`
				      AND `products`.`sizeid` = `stylestosizes`.`sizeid`)
				SET `products`.`inactive` = 1
				WHERE (`stylestocolours`.`id` IS NULL
				       OR `stylestosizes`.`id` IS NULL)
				      AND `products`.`styleid`='".$styleid."'";
	$productresult = $this->db->query($deletestatement);

	if(!$productresult)
		$error= new appError(-310,"Error 310.","Could Not Update Product Records");






	}//end function --importBleepProductRecords--


}//end class --importBleepProducts--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepProducts($db);
    $clean->importBleepProductRecords();

}//end if
?>
