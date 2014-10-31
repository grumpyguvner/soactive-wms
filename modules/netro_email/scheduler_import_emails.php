<?php

include("../../include/session.php");
include_once("include/email_reader.php");

class importNetroEmails{

	var $db;
	var $mailbox;

	function importNetroEmails($db){
		$this->db = $db;

		$log = new phpbmsLog("Connecting to email inbox", "Import Netro Emails");
	
		$this->mailbox=pop3_login("m.sheactive.net","sales.sheactive","5h3Act!ve");
		if (!$this->mailbox){
			$log = new appError(0, "unable to connect to email inbox", "Import Netro Emails");
		}
	}//end method --importNetroEmails--

	//This method should import new and updated records
	//from the netro mailbox
	function processMailbox(){

//		$logError = new appError(0, "Error Details", "Import Netro Emails"); //also stops execution!

//		$message = "Import Netro Emails: Hello World (number)";
//		$log = new phpbmsLog($message, "SCHEDULER");

		//////////////////////////////////////////////////////////////////////////////////////////
		//                                                                                      //
		// This import rountine reads sequentially through the bleep import data and            //
		// creates/updates as necessary but (very importantly) adds dates for created and       //
		// modified so that we can use these dates for information that has been changed!!      //
		//                                                                                      //
		// WE ARE ONLY CHECKING CURRENTLY ENABLED STYLES WITH STOCK (PER BLEEP!)                //
		//                                                                                      //
		// In addition, it also sets the field bleep_lastimport so that we know when we last    //
		// check the source data ..                                                             //
		//                                                                                      //
		//////////////////////////////////////////////////////////////////////////////////////////

		$id = $this->getNextStyleID();
		//and lets set a throttle
		$throttle = "0";
                if(defined("BLEEP_STYLETHROTTLE"))
                    $throttle = BLEEP_STYLETHROTTLE;


		$bleep_statement = "SELECT
					STYLES.id AS id,
					STYLES.description AS stylename,
					datafeeds_style_descriptions.STYLES_description AS description,
					STYLES.supplier AS supplier,
					STYLES.department AS department,
					STYLES.web_price as web_price,
					STYLES.web_promo_price AS web_promo_price,
					STYLES.web_promo_start AS web_promo_start,
					STYLES.web_promo_end AS web_promo_end,
					STYLES.weight AS weight,
					STYLES.current_stock AS bleep_stock,
					CONCAT(STYLES.department,'_',STYLES.user_string_1) AS alternate_depts,
					froogle.description AS webdescription
					FROM STYLES 
						LEFT JOIN froogle ON STYLES.id = froogle.id
						LEFT JOIN datafeeds_style_descriptions 
						  ON STYLES.id = datafeeds_style_descriptions.STYLES_id
					WHERE STYLES.id >".$id." AND 
					STYLES.current_stock > 0";

		if(!$throttle=="0")
			$bleep_statement .= " LIMIT ".$throttle;
//$log = new phpbmsLog($bleep_statement, "DEBUG STYLE");

		$bleep_result = $this->bleep_db->query($bleep_statement);

		if(!$bleep_result){
			$error= new appError(0,"Could Not Retrieve Styles from Bleep Database","Import Netro Emails");
		}

		if (!$this->bleep_db->numRows($bleep_result)){
			//if no records are found then we must have got to the end of the records
			//so reset the counter ...
			$this->updateStyleID("0");
			return true;
		}

		while($bleep_record=$this->bleep_db->fetchArray($bleep_result)){

			//We need to make some basic overides to the bleep record
			$bleep_record["id"] = str_pad($bleep_record["id"], 4, "0", STR_PAD_LEFT);
			$this->updateStyleID($bleep_record["id"]);
			$mythumbnail = "http://www.sheactive.co.uk";
			$mythumbnail .= "/images/items/alt_thumb/";
			$mythumbnail .= $bleep_record["id"].".jpg";
			$mypicture = "http://www.sheactive.co.uk";
			$mypicture .= "/images/items/";
			$mypicture .= $bleep_record["id"].".jpg";

//
//TODO: Promotion Start & End Dates
//seem to be having real problems getting the date to save in the correct format (even from the edit page)
//need to come back and revisit.
//			if($bleep_record["web_promo_start"]=="") $bleep_record["web_promo_start"] = NULL;
//			$bleep_record["web_promo_start"] = stringToDate($bleep_record["web_promo_start"],"English, UK");
//			$bleep_record["web_promo_start"] = dateToString($bleep_record["web_promo_start"],"SQL")." 00:00:00";
//			if($bleep_record["web_promo_end"]=="") $bleep_record["web_promo_end"] = NULL;
//

			$message = $bleep_record["id"]." '".$bleep_record["stylename"]."' ";
//			$log = new phpbmsLog($message, "BLEEP_STYLE");
$log = new phpbmsLog("bleep import style:".$bleep_record["id"]." - removing this causes an ERROR!", "TODO");
//$log = new phpbmsLog("name=".$bleep_record["stylename"], "DEBUG STYLE");
//$log = new phpbmsLog("desc=".$bleep_record["description"], "DEBUG STYLE");
//$log = new phpbmsLog(" web=".$bleep_record["webdescription"], "DEBUG STYLE");

			//Before we start lets retrieve valuse for existing records
			$mycolours = $this->generateColourString($bleep_record["id"]);
//$log = new phpbmsLog("style 2=".$mycolours, "DEBUG STYLE");
			$updatecolours = false;
			$mysizes = $this->generateSizeString($bleep_record["id"]);
//$log = new phpbmsLog("style 3=".$mysizes, "DEBUG STYLE");
			$updatesizes = false;
			$mysupplierid = $this->getSupplierID($bleep_record["supplier"]);
//$log = new phpbmsLog("style 4=".$mysupplierid, "DEBUG STYLE");
			$mycategoryid = $this->getCategoryID($bleep_record["department"]);
			$updatecategories = false;
//$log = new phpbmsLog("style 5=".$mycategoryid, "DEBUG STYLE");

			//find the existing record (if one exists)
			$querystatement="SELECT uuid FROM styles WHERE stylenumber=".((int) $bleep_record["id"]);
			$queryresult = $this->db->query($querystatement);
			if(!$queryresult){
				$error= new appError(0,"Could Not Retrieve Styles","Import Netro Emails");
			}

//$log = new phpbmsLog("style 6", "DEBUG STYLE");
			$found = false;
			while($therecord=$this->db->fetchArray($queryresult)){
				$found = true;
				$myuuid = $therecord["uuid"];

				$styles = new phpbmsTable($this->db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
				$therecord = $styles->getRecord($therecord["uuid"],true);
				$variables = array();

				$changed = false;
				$translation = false;
			        foreach($therecord as $name => $field){

					switch($name){

						//the following variables may change
						case "stylename":
							$variables[$name] = $bleep_record["stylename"];
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "name ";
								$changed = true;
								$translation = true;
							}
							break;

						case "description":
							$variables[$name] = $bleep_record["description"];
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "description ";
								$changed = true;
								$translation = true;
							}
							break;

						case "categoryid":
							$variables[$name] = $mycategoryid;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "department ";
								$changed = true;
							}
							break;

						case "supplierid":
							$variables[$name] = $mysupplierid;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "supplier ";
								$changed = true;
							}
							break;

						case "unitprice":
							$variables[$name] = $bleep_record["web_price"];
							if($variables[$name]!=$therecord[$name]){
								$message .= "price ";
								$changed = true;
							}
							break;

						case "saleprice":
							$variables[$name] = $bleep_record["web_promo_price"];
							if($variables[$name]!=$therecord[$name]){
								$message .= "sale price ";
								$changed = true;
							}
							break;

						case "bleep_stock":
							$variables[$name] = $bleep_record["bleep_stock"];
							if($variables[$name]!=$therecord[$name]){
								$message .= "stock ";
								$changed = true;
							}
							break;

//						case "salestartdate":
//							$variables[$name] = stringToDate($bleep_record["web_promo_start"],"SQL");
//							if($variables[$name]!=$therecord[$name]) $changed = true;
//							break;

//						case "saleenddate":
//							$variables[$name] = $bleep_record["web_promo_end"];
//							if($variables[$name]!=$therecord[$name]) $changed = true;
//							break;

						case "webdescription":
							$variables[$name] = $bleep_record["webdescription"];
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "webdescription ";
								$changed = true;
								$translation = true;
							}
							break;

						case "thumbnail":
							$variables[$name] = $mythumbnail;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "thumbnail ";
								$changed = true;
							}
							break;

						case "picture":
							$variables[$name] = $mypicture;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "picture ";
								$changed = true;
							}
							break;

						case "bleep_alt_depts":
							$variables[$name] = $bleep_record["alternate_depts"];
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "alt depts ";
								$updatecategories = true;
							}
							break;

						case "bleep_colours":
							$variables[$name] = $mycolours;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "colours ";
								$updatecolours = true;
							}
							break;

						case "bleep_sizes":
							$variables[$name] = $mysizes;
							if(strcmp($variables[$name],$therecord[$name])){
								$message .= "sizes ";
								$updatesizes = true;
							}
							break;

//						case "webenabled":
//							$variables["webenabled"] = false;
//							if($bleep_record["web_enabled"]) $variables["webenabled"] = true;
//							if($variables[$name]!=$therecord[$name]){
//								$message .= "on web? ";
//								$changed = true;
//							}
//							break;


//						case "status":
//							if($bleep_record["current_stock"]>0){
//								$variables["status"] = "In Stock";
//							} else {
//								$variables["status"] = "Out of Stock";
//							}
//							if(strcmp($variables["status"],$therecord["status")) $changed = true;
//							break;

						default://copy all remaining fields over
							$variables[$name] = $field;
							break;

					}//end switch

				}//endforeach

//$log = new phpbmsLog("style 7", "DEBUG STYLE");
				$variables = $styles->prepareVariables($variables);
				$styleVerify = $styles->verifyVariables($variables);
				if(!count($styleVerify)){//check for errors
					if($changed){
						$message .= " - Updated (".$therecord["uuid"].") ";
						$log = new phpbmsLog($message, "BLEEP_STYLE");
						if($translation) 
							$variables["translationrequired"] = sqlTimeFromString(timeToString(time()));

						$styles->updateRecord($variables,BLEEP_IMPORTUSER,true);

//						$translate = new GoogleTranslateApi();
//						$message = $therecord["webdescription"]."=";
//						$message .= $translate->translate($therecord["webdescription"]);
//						$log = new phpbmsLog($message, "TRANSLATION");

					} else {
						$message .= " - Unchanged (".$therecord["uuid"].") ";
//						$log = new phpbmsLog($message, "BLEEP_STYLE");
					}
				}//insert if no errors

			}//end while

//$log = new phpbmsLog("style 8", "DEBUG STYLE");
			if(!$found){
				//couldn't find an existing record so create one
				$message .= " - Inserting new record ";
				$log = new phpbmsLog($message, "BLEEP_STYLE");
				$changed = true; //set the changed flag so that bleep_lastchanged gets set!

				//now we need to create style
				$styles = new phpbmsTable($this->db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
				$therecord = $styles->getDefaults();

				$variables = array();
				//load values from the bleep record:
				if($bleep_record["id"]) $variables["stylenumber"] = $bleep_record["id"];
				$variables["categoryid"] = $mycategoryid;
				if($bleep_record["stylename"]) $variables["stylename"] = $bleep_record["stylename"];
				if($bleep_record["description"]) $variables["description"] = $bleep_record["description"];
				if($mysupplierid) $variables["supplierid"] = $mysupplierid;
				if($bleep_record["web_price"]) $variables["unitprice"] = $bleep_record["web_price"];
				if($bleep_record["web_promo_price"]) $variables["saleprice"] = $bleep_record["web_promo_price"];
//				if($bleep_record["web_promo_start"]) $variables["salestartdate"] = $bleep_record["web_promo_start"];
//				if($bleep_record["web_promo_end"]) $variables["saleenddate"] = $bleep_record["web_promo_end"];
//				if($bleep_record["web_vat_inc"]) $variables["taxable"] = $bleep_record["web_vat_inc"];
				if($bleep_record["weight"]) $variables["weight"] = $bleep_record["weight"];
				if($bleep_record["bleep_stock"]) $variables["bleep_stock"] = $bleep_record["bleep_stock"];
				if($bleep_record["webdescription"]) $variables["webdescription"] = $bleep_record["webdescription"];
				if($mythumbnail) $variables["thumbnail"] = $mythumbnail;
				if($mypicture) $variables["picture"] = $mypicture;
				if($bleep_record["alternate_depts"]){
					$variables["bleep_alt_depts"] = $bleep_record["alternate_depts"];
					$updatecategories = true;
				}
				if($mycolours){
					$variables["bleep_colours"] = $mycolours;
					$updatecolours = true;
				}
				if($mysizes){
					$variables["bleep_sizes"] = $mysizes;
					$updatesizes = true;
				}

				//load other default values:
				$variables["type"] = "Inventory";
//				if($bleep_record["current_stock"]>0){
//					$variables["status"] = "In Stock";
//				} else {
//					$variables["status"] = "Out of Stock";
//				}
				$variables["packagesperitem"] = ((int) 1);
				$variables["webenabled"] = false;
				$variables["translationrequired"] = sqlTimeFromString(timeToString(time()));
				//if($bleep_record["web_enabled"]) $variables["webenabled"] = true;

				//Variables still to sort from phpbms side
				// $variables["unitcost"] = ((int) 0);//need to amend bleep output
				// $variables["unitofmeasure"] = ((int) 0);//???
				// $variables["keywords"] = ((int) 0);//???
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
				$styleVerify = $styles->verifyVariables($variables);
				if(!count($styleVerify))//check for errors
					$therecord = $styles->insertRecord($variables,BLEEP_IMPORTUSER,false,false,true);//insert if no errors
				$myuuid = $therecord["uuid"];
			}//endif record found


//$log = new phpbmsLog("style 9", "DEBUG STYLE");
			if ($updatecategories){
				$alt_departments = explode("_", $bleep_record["alternate_depts"]);

				//first remove any existing records
				$deletestatement = "
					DELETE FROM
						`stylestostylecategories`
						WHERE
						`styleid` = '".$myuuid."'
					";

				$this->db->query($deletestatement);

				foreach($alt_departments as $item){

					$styleCategory = $this->getCategoryID($item);

					if(!$styleCategory==""){
						$insertstatement = "
							INSERT INTO
								`stylestostylecategories`
								(styleid, stylecategoryid)
							VALUES
								(
								'".$myuuid."',
								'".$styleCategory."'
								)";

						$this->db->query($insertstatement);
					}

				}//endforeach

			}//endif update additional categories


//$log = new phpbmsLog("style 10", "DEBUG STYLE");
			if ($updatecolours){
				$colours = explode("_", $mycolours);

				//first remove any existing records
				$deletestatement = "
					DELETE FROM
						`stylestocolours`
						WHERE
						`styleid` = '".$myuuid."'
					";

				$this->db->query($deletestatement);

				foreach($colours as $item){

					$styleColour = $this->getColourID($item);

					$mythumbnail = "http://www.sheactive.co.uk";
					$mythumbnail .= "/images/items/alt_thumb/";
					$mythumbnail .= $bleep_record["id"].$item.".jpg";
					$mypicture = "http://www.sheactive.co.uk";
					$mypicture .= "/images/items/";
					$mypicture .= $bleep_record["id"].$item.".jpg";

					if(!$styleColour==""){
						$insertstatement = "
							INSERT INTO
								`stylestocolours`
								(styleid, colourid, bleepid, thumbnail, picture)
							VALUES
								(
								'".$myuuid."',
								'".$styleColour."',
								'".$item."',
								'".$mythumbnail."',
								'".$mypicture."'
								)";
//						$insertstatement = "
//							INSERT INTO
//								`stylestocolours`
//								(styleid, colourid, bleepid)
//							VALUES
//								(
//								'".$myuuid."',
//								'".$styleColour."',
//								'".$item."'
//								)";

						$this->db->query($insertstatement);
					}

				}//endforeach

			}//endif update available colours


//$log = new phpbmsLog("style 11", "DEBUG STYLE");
			if ($updatesizes){
				$sizes = explode("_", $mysizes);

				//first remove any existing records
				$deletestatement = "
					DELETE FROM
						`stylestosizes`
						WHERE
						`styleid` = '".$myuuid."'
					";

				$this->db->query($deletestatement);

				foreach($sizes as $item){

					$styleSize = $this->getSizeID($item);

					if(!$styleSize==""){
						$insertstatement = "
							INSERT INTO
								`stylestosizes`
								(styleid, sizeid, bleepid)
							VALUES
								(
								'".$myuuid."',
								'".$styleSize."',
								'".$item."'
								)";

						$this->db->query($insertstatement);
					}

				}//endforeach

			}//endif update available sizes

			//update the bleep_lastimport date so that we know it has been done (even if nothing changed!)
			$querystatement="UPDATE styles 
					  SET bleep_lastimport=NOW()";
			// We cant use last modified because it has changed simply by updating bleep_lastimport!
			if($changed) $querystatement.=", bleep_lastchanged=NOW()";
			$querystatement.=" WHERE uuid='".$myuuid."'";
			$queryresult = $this->db->query($querystatement);
			if(!$queryresult){
				$error= new appError(0,"Could Not Update Import Date","Import Netro Emails");
			}

////$log = new phpbmsLog("style 12", "DEBUG STYLE");
//			$product = new importBleepProducts($this->db);
//			$product->importBleepProductRecords($bleep_record["id"]);

//$log = new phpbmsLog("style 13", "DEBUG STYLE");
		}//end while loop

//$log = new phpbmsLog("style 14", "DEBUG STYLE");
	}//end method --importNetroEmails-

    function getNextStyleID(){

	//
	//find and return the next style id to process (if one exists)
	//
//$log = new phpbmsLog("step 1", "BLEEP_STYLE");
	$mystyleid = false;

	$querystatement="SELECT value FROM settings WHERE name='bleep_laststyleid'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Last Style ID Setting");
		return "0";

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mystyleid = $therecord["value"];

		}//end while

	}//endif queryresult

        return $mystyleid;

    }//end function getNextStyleID

    function updateStyleID($bleepid){

	//
	//update settings file with the last attempted styleid
	//we do not care if it is successfull, we skip to the next anyway
	//otherwise we could hang on one record indefinitely!
	//
//$log = new phpbmsLog("styleid=".$bleepid, "DEBUG");
	$querystatement="UPDATE settings SET value = ('".$bleepid."') WHERE name='bleep_laststyleid'";
	$queryresult = $this->db->query($querystatement);
	if(!$queryresult)
		$error= new appError(-310,"Error 310.","Unable to update Last Style ID Setting");


    }//end function getNextStyleID

    function generateColourString($bleepid){
	//
	//find and return the existing colours as a concatenated string
	//
	$mycolours = "";

	$querystatement="SELECT DISTINCT `colour` FROM PRODUCTS WHERE `style`=".((int) $bleepid);
	$queryresult = $this->bleep_db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Product Colours");
		$mycolours = false;

	} else {
		while($therecord=$this->bleep_db->fetchArray($queryresult)){

			$mycolours .= str_pad($therecord["colour"], 4, "0", STR_PAD_LEFT)."_";

		}//end while

	}//endif queryresult
	//
	//we should now have the colours
	//just need to remove trailing separator
	//

        return substr($mycolours, 0, -1);

    }//end function generateColourString

    function generateSizeString($bleepid){
	//
	//find and return the existing sizes as a concatenated string
	//
	$mysizes = "";

	$querystatement="SELECT DISTINCT `size` FROM PRODUCTS WHERE `style`=".((int) $bleepid);
	$queryresult = $this->bleep_db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Product Sizes");
		$mysizes = false;

	} else {
		while($therecord=$this->bleep_db->fetchArray($queryresult)){

			$mysizes .= str_pad($therecord["size"], 3, "0", STR_PAD_LEFT)."_";

		}//end while

	}//endif queryresult
	//
	//we should now have the sizes
	//just need to remove trailing separator
	//

        return substr($mysizes, 0, -1);

    }//end function generateSizeString

/*
   Author:    Mark Horton (mark@hortonconsulting.co.uk)
    
    Important:
           These are common functions to be included WITHIN each bleep import class
           they basically provide common functionality across classes
                
*/

    function getCategoryID($bleepid){
	//
	//find and return the existing department category uuid (if one exists)
	//
	$mycategoryid = "";

	$querystatement="SELECT categoryid FROM departments WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve departments");
		$mycategoryid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mycategoryid = $therecord["categoryid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a categoryid
	//

        return $mycategoryid;

    }//end function getCategoryID

    function getColourID($bleepid){
	//
	//find and return the existing colour uuid (if one exists)
	//
	$mycolourid = "";

	$querystatement="SELECT uuid FROM colours WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve colours");
		$mycolourid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mycolourid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a colourid
	//

        return $mycolourid;

    }//end function getColourID

    function getLocationID($bleepid){
	//
	//find and return the existing location uuid (if one exists)
	//
	$mylocationid = "";

	$querystatement="SELECT uuid FROM locations WHERE bleepid='".$bleepid."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve locations");
		$mylocationid = false;

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
	$myproductid = "";

	$querystatement="SELECT uuid FROM products WHERE bleepid=".$bleepid;
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve products");
		$myproductid = false;

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

    function getSizeID($bleepid){
	//
	//find and return the existing size uuid (if one exists)
	//
	$mysizeid = "";

	$querystatement="SELECT uuid FROM sizes WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve sizes");
		$mysizeid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mysizeid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a sizeid
	//

        return $mysizeid;

    }//end function getSizeID

    function getStyleID($bleepid){
	//
	//find and return the existing style uuid (if one exists)
	//
	$mystyleid = "";

	$querystatement="SELECT uuid FROM styles WHERE stylenumber='".($bleepid)."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve styles");
		$mystyleid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mystyleid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a styleid
	//

        return $mystyleid;

    }//end function getStyleID

    function getSupplierID($bleepid){
	//
	//find and return the existing supplier uuid (if one exists)
	//
	$mysupplierid = "";

	$querystatement="SELECT uuid FROM suppliers WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Suppliers");
		$mysupplierid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mysupplierid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a supplierid
	//

        return $mysupplierid;

    }//end function getSupplierID


}//end class --importNetroEmails--

if(!isset($noOutput) && isset($db)){

    $clean = new importNetroEmails($db);
    $clean->processMailbox();

}//end if
?>
