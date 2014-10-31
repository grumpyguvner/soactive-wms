<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$success = true;

	//We need to set ALL stock to zero before import starts
echo "<br>resetting all stock quantities to zero.";
	$querystatement="UPDATE productsbylocation SET quantity=0";
	$queryresult = $db->query($querystatement);

        $locations = array("WEBSTORE", "BRIGHTON", "CGARDEN", "WHSE");


	foreach ($locations AS $location) {
            importStockRecords($db, $location);
	}//end foreach

	if(!$success){
		echo "<br>Import Failed!";
	} else {
		echo "<br>Import Successful!";
	}//endif loadBleepDB

    function importStockRecords($db, $location){
echo "<br><br><br><br><br><br><br><br>";
echo "<br>#####################################################################";
echo "<br>IMPORTING STOCK FOR ".$location;
echo "<br>#####################################################################";

        include_once("include/bleep_db.php");
        $bleep_db = new bleep_db();

        if($bleep_db==NULL){
                $error=new appError(-310,"","Database not loaded");
                $success = false;
        }

        $bleep_db->logError = false;
        $bleep_db->stopOnError = false;

        $bleep_db->setEncoding($encoding);

        $bleep_db->logError = true;

        $bleep_statement = "SELECT
                                ".$location.".product_id AS id,
                                ".$location.".current_stock AS current_stock
                                FROM ".$location." WHERE current_stock <> 0
                                ";
echo "<br>".$bleep_statement;

        $bleep_result = $bleep_db->query($bleep_statement);

        if(!$bleep_result){
                $error= new appError(-310,"Error 310.","Could Not Retrieve Stock Records From Bleep Database");
                $success = false;

        } else {
                while($bleep_record=$bleep_db->fetchArray($bleep_result)){
echo "<br>".$bleep_record["id"];

                        //Before we start lets retrieve valuse for existing records
                        $myproductid = getProductID($db, $bleep_record["id"]);
                        $mylocationid = getLocationID($db, $location);

                        //We can only continue if value exists
                        if(($myproductid!="")&&($mylocationid!="")){
echo " :".$mylocationid."/".$myproductid.": ";

                                //find the existing record (if one exists)
                                $querystatement="SELECT
                                                        uuid
                                                        FROM productsbylocation
                                                        WHERE productid='".$myproductid."'
                                                        AND locationid='".$mylocationid."'";
                                $queryresult = $db->query($querystatement);

                                if(!$queryresult){
                                        $error= new appError(-310,"Error 310.","Could Not Retrieve Stock");
                                        $success = false;

                                } else {
                                        $found = false;
                                        while($therecord=$db->fetchArray($queryresult)){
                                                $found = true;
                                                $myuuid = $therecord["uuid"];

                                                $products = new phpbmsTable($db,"tbld:cf6413ca-d3db-eeea-4841-e2490ef50ef0");
                                                $therecord = $products->getRecord($therecord["uuid"],true);
                                                $variables = array();

                                                $changed = false;
                                                foreach($therecord as $name => $field){

                                                        switch($name){

                                                                //the following variables may change
                                                                case "locationid":
                                                                        $variables[$name] = $mylocationid;
                                                                        if(strcmp($variables[$name],$therecord[$name])) $changed = true;
                                                                        break;

                                                                case "productid":
                                                                        $variables[$name] = $myproductid;
                                                                        if(strcmp($variables[$name],$therecord[$name])) $changed = true;
                                                                        break;

                                                                case "quantity":
                                                                        $variables[$name] = $bleep_record["current_stock"];
                                                                        if($variables[$name]!=$therecord[$name]) $changed = true;
                                                                        break;

//								case "status":
//										if($bleep_record["current_stock"]>0){
//											$variables["status"] = "In Stock";
//										} else {
//											$variables["status"] = "Out of Stock";
//										}
//										if(strcmp($variables["status"],$therecord["status")) $changed = true;
//										break;

                                                                default://copy all remaining fields over
                                                                        $variables[$name] = $field;
                                                                        break;

                                                        }//end switch

                                                }//endforeach

                                                $variables = $products->prepareVariables($variables);
                                                $departmentVerify = $products->verifyVariables($variables);
                                                if(!count($departmentVerify)){//check for errors
                                                        if($changed){
echo " - Updated (".$therecord["uuid"].") ";
                                                                $products->updateRecord($variables,NULL,true);
                                                        } else {
echo " - Unchanged (".$therecord["uuid"].") ";
                                                        }
                                                }//insert if no errors

                                        }//end while

                                        if(!$found){
                                                //couldn't find an existing record so create one
echo " - Inserting new record ";

                                                $products = new phpbmsTable($db,"tbld:cf6413ca-d3db-eeea-4841-e2490ef50ef0");
                                                $therecord = $products->getDefaults();

                                                $variables = array();
                                                //load values from the bleep record:
                                                $variables["locationid"] = $mylocationid;
                                                $variables["productid"] = $myproductid;
                                                if($bleep_record["current_stock"]) $variables["quantity"] = $bleep_record["current_stock"];

                                                $variables = $products->prepareVariables($variables);
                                                $departmentVerify = $products->verifyVariables($variables);
                                                if(!count($departmentVerify))//check for errors
                                                        $therecord = $products->insertRecord($variables,NULL,false,false,true);//insert if no errors
                                                $myuuid = $therecord["uuid"];

                                        }

                                }//endif queryresult


                        } else {
echo " - skipped!";

                        }//endif style, colour & size exists

                }//end while

        }//endif bleep_result
    }

    function getLocationID($db, $bleepid){
	//
	//find and return the existing location uuid (if one exists)
	//
	$mylocationid = "";

	$querystatement="SELECT uuid FROM locations WHERE bleepid='".$bleepid."'";
	$queryresult = $db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve locations");
		$mylocationid = false;

	} else {
		while($therecord=$db->fetchArray($queryresult)){

			$mylocationid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a locationid
	//

        return $mylocationid;

    }//end function getLocationID


    function getProductID($db, $bleepid){
	//
	//find and return the existing product uuid (if one exists)
	//
	$myproductid = "";

	$querystatement="SELECT uuid FROM products WHERE bleepid=".$bleepid;
	$queryresult = $db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve products");
		$myproductid = false;

	} else {
		while($therecord=$db->fetchArray($queryresult)){

			$myproductid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a productid
	//

        return $myproductid;

    }//end function getProductID

?>
