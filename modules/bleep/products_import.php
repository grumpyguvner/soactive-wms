<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$success = true;

	include_once("include/bleep_db.php");
	$bleep_db = new bleep_db();

	if($bleep_db==NULL){
		$error=new appError(-310,"","Database not loaded");
		$success = false;

	} else {
		$bleep_db->logError = false;
		$bleep_db->stopOnError = false;

		$bleep_db->setEncoding($encoding);

		$bleep_db->logError = true;

		$bleep_statement = "SELECT
					PRODUCTS.id AS id,
					PRODUCTS.style AS style,
					PRODUCTS.colour AS colour,
					PRODUCTS.size as size,
					ECWAREHOUSEDB.cost_price as unitcost,
					ECWAREHOUSEDB.season as season
					FROM PRODUCTS
                                                LEFT JOIN froogle ON PRODUCTS.id = froogle.id
                                                LEFT JOIN ECWAREHOUSEDB ON (
                                                            PRODUCTS.style = ECWAREHOUSEDB.style
                                                        AND PRODUCTS.colour = ECWAREHOUSEDB.colour
                                                        AND PRODUCTS.size = ECWAREHOUSEDB.size )
					";

		$bleep_result = $bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(-310,"Error 310.","Could Not Retrieve Products From Bleep Database");
			$success = false;

		} else {
			while($bleep_record=$bleep_db->fetchArray($bleep_result)){
				//We need to make some basic overides to the bleep record
				$bleep_record["style"] = str_pad($bleep_record["style"], 4, "0", STR_PAD_LEFT);
				$bleep_record["colour"] = str_pad($bleep_record["colour"], 4, "0", STR_PAD_LEFT);
				$bleep_record["size"] = str_pad($bleep_record["size"], 3, "0", STR_PAD_LEFT);
//
//TODO: Promotion Start & End Dates
//seem to be having real problems getting the date to save in the correct format (even from the edit page)
//need to come back and revisit.
//				if($bleep_record["web_promo_start"]=="") $bleep_record["web_promo_start"] = NULL;
//				$bleep_record["web_promo_start"] = stringToDate($bleep_record["web_promo_start"],"English, UK");
//				$bleep_record["web_promo_start"] = dateToString($bleep_record["web_promo_start"],"SQL")." 00:00:00";
//				if($bleep_record["web_promo_end"]=="") $bleep_record["web_promo_end"] = NULL;
//
echo "<br>".$bleep_record["id"];

				//Before we start lets retrieve valuse for existing records
echo "<br>stepa:".$bleep_record["style"].":";
				$mystyleid = $bleep_db->getStyleID($db,$bleep_record["style"]);
echo "<br>stepb";
				$mycolourid = $bleep_db->getColourID($db,$bleep_record["colour"]);
echo "<br>stepc";
				$mysizeid = $bleep_db->getSizeID($db,$bleep_record["size"]);
echo "<br>step1";

//echo "<br>style=".$bleep_record["style"]."=".$mystyleid;
//echo "<br>colour=".$bleep_record["colour"]."=".$mycolourid;
//echo "<br>size=".$bleep_record["size"]."=".$mysizeid;

				//We can only continue if all values exist
				if(($mystyleid!="")&&($mycolourid!="")&&($mysizeid!="")){

					//find the existing record (if one exists)
					$querystatement="SELECT uuid 
								FROM products
								WHERE (styleid='".$mystyleid."'
								  AND colourid='".$mycolourid."'
								  AND sizeid='".$mysizeid."')";
					$queryresult = $db->query($querystatement);
echo "<br>step2";

					if(!$queryresult){
						$error= new appError(-310,"Error 310.","Could Not Retrieve Products");
						$success = false;

					} else {
						$found = false;
						while($therecord=$db->fetchArray($queryresult)){
							$found = true;
							$myuuid = $therecord["uuid"];
echo "<br>step3";

							$products = new phpbmsTable($db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
							$therecord = $products->getRecord($therecord["uuid"],true);
							$variables = array();

							$changed = false;
						        foreach($therecord as $name => $field){

								switch($name){

									//the following variables may change
									case "bleepid":
echo "<br>step4";
										$variables[$name] = $bleep_record["id"];
										if($variables[$name]!=$therecord[$name]) $changed = true;
										break;

									case "unitcost":
echo "<br>step5";
										$variables[$name] = $bleep_record["unitcost"];
										if($variables[$name]!=$therecord[$name]) $changed = true;
										break;

									case "season":
echo "<br>step6";
										$variables[$name] = $bleep_record["season"];
										if(strcmp($variables[$name],$therecord[$name])) $changed = true;
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
echo "<br>step7";
										$variables[$name] = $field;
										break;

								}//end switch

							}//endforeach

echo "<br>step8";
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

							$products = new phpbmsTable($db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
							$therecord = $products->getDefaults();

							$variables = array();
							//load values from the bleep record:
							if($bleep_record["id"]) $variables["bleepid"] = $bleep_record["id"];
							$variables["styleid"] = $mystyleid;
							$variables["colourid"] = $mycolourid;
							$variables["sizeid"] = $mysizeid;
							if($bleep_record["unitcost"]) $variables["unitcost"] = $bleep_record["unitcost"];
							if($bleep_record["season"]) $variables["season"] = $bleep_record["season"];

							//load other default values:
//							if($bleep_record["current_stock"]>0){
//								$variables["status"] = "In Stock";
//							} else {
//								$variables["status"] = "Out of Stock";
//							}
							$variables["webenabled"] = true;

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

	}//endif database connect

	if(!$success){
		echo "<br>Import Failed!";
	} else {
		echo "<br>Import Successful!";
	}//endif loadBleepDB

?>
