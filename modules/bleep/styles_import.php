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
					ALSTYLES.id AS id,
					ALSTYLES.description AS description,
					ALSTYLES.supplier AS supplier,
					ALSTYLES.department AS department,
					ALSTYLES.web_price as web_price,
					ALSTYLES.web_promo_price AS web_promo_price,
					ALSTYLES.web_promo_start AS web_promo_start,
					ALSTYLES.web_promo_end AS web_promo_end,
					ALSTYLES.vat_inc AS web_vat_inc,
					ALSTYLES.weight AS weight,
					ALSTYLES.current_stock AS current_stock,
					ALSTYLES.user_string_1 AS alternate_depts,
					froogle.description AS webdescription,
					STYLES.id AS web_enabled
					FROM ALSTYLES 
						LEFT JOIN froogle ON ALSTYLES.id = froogle.id
						LEFT JOIN STYLES ON ALSTYLES.id = STYLES.id
					";

		$bleep_result = $bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(-310,"Error 310.","Could Not Retrieve Styles From Bleep Database");
			$success = false;

		} else {
			while($bleep_record=$bleep_db->fetchArray($bleep_result)){
				//We need to make some basic overides to the bleep record
				$bleep_record["id"] = str_pad($bleep_record["id"], 4, "0", STR_PAD_LEFT);
//
//TODO: Promotion Start & End Dates
//seem to be having real problems getting the date to save in the correct format (even from the edit page)
//need to come back and revisit.
//				if($bleep_record["web_promo_start"]=="") $bleep_record["web_promo_start"] = NULL;
//				$bleep_record["web_promo_start"] = stringToDate($bleep_record["web_promo_start"],"English, UK");
//				$bleep_record["web_promo_start"] = dateToString($bleep_record["web_promo_start"],"SQL")." 00:00:00";
//				if($bleep_record["web_promo_end"]=="") $bleep_record["web_promo_end"] = NULL;
//
echo "<br>".$bleep_record["id"]." '".$bleep_record["description"]."'";

				//Before we start lets retrieve valuse for existing records
				$mysupplierid = $bleep_db->getSupplierID($db,$bleep_record["supplier"]);
				$mycategoryid = $bleep_db->getCategoryID($db,$bleep_record["department"]);
				$updatecategories = false;


				//find the existing record (if one exists)
				$querystatement="SELECT uuid FROM styles WHERE stylenumber=".((int) $bleep_record["id"]);
				$queryresult = $db->query($querystatement);

				if(!$queryresult){
					$error= new appError(-310,"Error 310.","Could Not Retrieve Styles");
					$success = false;

				} else {
					$found = false;
					while($therecord=$db->fetchArray($queryresult)){
						$found = true;
						$myuuid = $therecord["uuid"];

						$styles = new phpbmsTable($db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
						$therecord = $styles->getRecord($therecord["uuid"],true);
						$variables = array();

						$changed = false;
					        foreach($therecord as $name => $field){

							switch($name){

								//the following variables may change
								case "stylename":
									$variables[$name] = $bleep_record["description"];
									if(strcmp($variables[$name],$therecord[$name])) $changed = true;
									break;

								case "categoryid":
									$variables[$name] = $mycategoryid;
									if(strcmp($variables[$name],$therecord[$name])) $changed = true;
									break;

								case "supplierid":
									$variables[$name] = $mysupplierid;
									if(strcmp($variables[$name],$therecord[$name])) $changed = true;
									break;

								case "unitprice":
									$variables[$name] = $bleep_record["web_price"];
									if($variables[$name]!=$therecord[$name]) $changed = true;
									break;

								case "saleprice":
									$variables[$name] = $bleep_record["web_promo_price"];
									if($variables[$name]!=$therecord[$name]) $changed = true;
									break;

//								case "salestartdate":
//									$variables[$name] = stringToDate($bleep_record["web_promo_start"],"SQL");
//									if($variables[$name]!=$therecord[$name]) $changed = true;
//									break;

//								case "saleenddate":
//									$variables[$name] = $bleep_record["web_promo_end"];
//									if($variables[$name]!=$therecord[$name]) $changed = true;
//									break;

								case "webdescription":
									$variables[$name] = $bleep_record["webdescription"];
									if(strcmp($variables[$name],$therecord[$name])) $changed = true;
									break;

								case "bleep_alt_depts":
									$variables[$name] = $bleep_record["alternate_depts"];
									if(strcmp($variables[$name],$therecord[$name])){
										$changed = true;
										$updatecategories = true;
									}
									break;

								case "webenabled":
									$variables["webenabled"] = false;
									if($bleep_record["web_enabled"]) $variables["webenabled"] = true;
									if($variables[$name]!=$therecord[$name]) $changed = true;
									break;


//								case "status":
//									if($bleep_record["current_stock"]>0){
//										$variables["status"] = "In Stock";
//									} else {
//										$variables["status"] = "Out of Stock";
//									}
//									if(strcmp($variables["status"],$therecord["status")) $changed = true;
//									break;

								default://copy all remaining fields over
									$variables[$name] = $field;
									break;

							}//end switch

						}//endforeach

						$variables = $styles->prepareVariables($variables);
						$departmentVerify = $styles->verifyVariables($variables);
						if(!count($departmentVerify)){//check for errors
							if($changed){
echo " - Updated (".$therecord["uuid"].") ";
								$styles->updateRecord($variables,NULL,true);
							} else {
echo " - Unchanged (".$therecord["uuid"].") ";
							}
						}//insert if no errors

					}//end while

					if(!$found){
						//couldn't find an existing record so create one
echo " - Inserting new record ";

						$styles = new phpbmsTable($db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
						$therecord = $styles->getDefaults();

						$variables = array();
						//load values from the bleep record:
						if($bleep_record["id"]) $variables["stylenumber"] = $bleep_record["id"];
						$variables["categoryid"] = $mycategoryid;
						if($bleep_record["description"]) $variables["stylename"] = $bleep_record["description"];
						if($mysupplierid) $variables["supplierid"] = $mysupplierid;
						if($bleep_record["web_price"]) $variables["unitprice"] = $bleep_record["web_price"];
						if($bleep_record["web_promo_price"]) $variables["saleprice"] = $bleep_record["web_promo_price"];
//						if($bleep_record["web_promo_start"]) $variables["salestartdate"] = $bleep_record["web_promo_start"];
//						if($bleep_record["web_promo_end"]) $variables["saleenddate"] = $bleep_record["web_promo_end"];
						if($bleep_record["web_vat_inc"]) $variables["taxable"] = $bleep_record["web_vat_inc"];
						if($bleep_record["weight"]) $variables["weight"] = $bleep_record["weight"];
						if($bleep_record["webdescription"]) $variables["webdescription"] = $bleep_record["webdescription"];
						if($bleep_record["alternate_depts"]){
							$variables["bleep_alt_depts"] = $bleep_record["alternate_depts"];
							$updatecategories = true;
						}

						//load other default values:
						$variables["type"] = "Inventory";
//						if($bleep_record["current_stock"]>0){
//							$variables["status"] = "In Stock";
//						} else {
//							$variables["status"] = "Out of Stock";
//						}
						$variables["packagesperitem"] = ((int) 1);
						$variables["webenabled"] = false;
						if($bleep_record["web_enabled"]) $variables["webenabled"] = true;

						//Variables still to sort from phpbms side
						// $variables["unitcost"] = ((int) 0);//need to amend bleep output
						// $variables["unitofmeasure"] = ((int) 0);//???
						// $variables["keywords"] = ((int) 0);//???
						// $variables["thumbnail"] = ((int) 0);//???
						// $variables["thumbnailmime"] = ((int) 0);//???
						// $variables["picture"] = ((int) 0);//???
						// $variables["picturemime"] = ((int) 0);//???
						// $variables["memo"] = "";//???
						// $variables["upc"] = "";//this will be stored at PLU level

						//Variables still to sort from bleep side
						// $variables[""] = $bleep_record["vat_rate"];
						// $variables[""] = $bleep_record["web_size"];
						// $variables[""] = $bleep_record["shipping_code"];
						// $variables[""] = $bleep_record["web_hot_deal"];
						// $variables[""] = $bleep_record["web_min_stock"];
						// $variables[""] = $bleep_record["web_oos_message"];
						// $variables[""] = $bleep_record["web_min_ord"];
						// $variables[""] = $bleep_record["web_check_stock"];
						// $variables[""] = $bleep_record["user_string_2"];
						// $variables[""] = $bleep_record["crossref_styles"];
						// $variables[""] = $bleep_record["user_string_4"];


						$variables = $styles->prepareVariables($variables);
						$departmentVerify = $styles->verifyVariables($variables);
						if(!count($departmentVerify))//check for errors
							$therecord = $styles->insertRecord($variables,NULL,false,false,true);//insert if no errors
						$myuuid = $therecord["uuid"];

					}

				}//endif queryresult


			if ($updatecategories){
				$alt_departments = explode("_", $bleep_record["alternate_depts"]);

				//first remove any existing records
				$deletestatement = "
					DELETE FROM
						`stylestostylecategories`
					WHERE
						`styleid` = '".$myuuid."'
					";

				$db->query($deletestatement);

				foreach($alt_departments as $item){

					$insertstatement = "
						INSERT INTO
							`stylestostylecategories`
							(styleid, stylecategoryid)
						VALUES
							(
							'".$myuuid."',
							'".$bleep_db->getCategoryID($db,$item)."'
							)";

					$db->query($insertstatement);

				}//endforeach

			}//endif update additional categories


			}//end while

		}//endif bleep_result

	}//endif database connect

	if(!$success){
		echo "<br>Import Failed!";
	} else {
		echo "<br>Import Successful!";
	}//endif loadBleepDB

?>
