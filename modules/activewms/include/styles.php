<?php

include_once("include/tables.php");
include_once("include/fields.php");

if(class_exists("phpbmsTable")){

	class styles extends phpbmsTable{

		var $availableStyles = NULL;

		function styles($db, $tabledefid = "tbld:7ecb8e4e-8301-11df-b557-00238b586e42", $backurl = NULL){
			parent::phpbmsTable($db, $tabledefid, $backurl);
		}//end function


		/**
		 * Overriden phpbmstable function
		 */
		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["type"] = "Inventory";
			$therecord["status"] = "In Stock";
			$therecord["taxable"] = 1;
			$therecord["inc_brighton"] = 0;
			$therecord["default_categoryid"] = "";
			$therecord["default_sportid"] = "";
			$therecord["categoryid"] = "";
			$therecord["supplierid"] = "";
			$therecord["addcats"] = array();
			$therecord["addcategories"] = array();
			$therecord["addsports"] = array();

			return $therecord;

		}//end function getDefaults


		/**
		* retruns the file contents uploaded by the form
		*
		* @param string $name name of the post field that was used to upload
		*
		* @retrun mixed the data from the post $name or FALSE on failure.
		*/
        	function getPicture($name){

			if (function_exists('file_get_contents'))
				$file = addslashes(file_get_contents($_FILES[$name]['tmp_name']));
			else {

				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name'])));

			}//endif

			return $file;

		}//end function getPicture

                /**
                 * function processAddEditPage
                 *
                 * Overrides the default processAddEditPage method to also return
                 *  an array containing references to the image ids.
                 */
                function processAddEditPage(){

			$therecord = parent::processAddEditPage();

                        $therecord['image_ids'] = array();
			$querystatement = "
				SELECT
				 `id`
				FROM
				 `styles_images`
				WHERE
				 `styleid`='".$therecord['uuid']."'
				ORDER BY
				 `displayorder`";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
                            while ($imagerecord = $this->db->fetchArray($queryresult)){
                                $therecord['image_ids'][] = $imagerecord['id'];
                            }
                        }

                        return $therecord;

                }//end function processAddEditPage

		/**
		* function getRecord
		*
		* Retrieves a single record from the database
		*
		* @param integer|string $id the record id or uuid
		* @param bool $useUuid specifies whther the $id is a uuid (true) or not.  Default is false
		*
		* @return array the record as an associative array
		*/

		function getRecord($id, $useUuid = false){

			$therecord = parent::getRecord($id, $useUuid);

			$therecord["catschanged"] = 1;
			$therecord["addcats"] = $this->getAdditionalStyleCategories($therecord["uuid"]);

			$therecord["categorieschanged"] = 1;
			$therecord["addcategories"] = $this->getAdditionalCategories($therecord["uuid"]);

			$therecord["sportschanged"] = 1;
			$therecord["addsports"] = $this->getAdditionalSports($therecord["uuid"]);

			return $therecord;

		}//end function getRecord

		/**
		* function getByReference
		*
		* Retrieves a single record from the database
		*
		* @param string $reference (the style number)
		*
		* @return array the record as an associative array
		*/

		function getByReference($reference){

			$whereclause = "`".$this->maintable."`.";
		
//			$id = (int) $id;
			$whereclause .= "`stylenumber` = '".$reference."'";
		
			// iterate through all possible fields and comprise a list
			// of columns to retrieve
		
			$fieldlist = "";
			foreach($this->fields as $fieldname => $thefield){
		
				if(isset($thefield["select"]))
				$fieldlist .= ", (".$thefield["select"].") AS `".$fieldname."`";
				else
				$fieldlist .= ", `".$fieldname."`";
		
			}//end foreach
		
			if($fieldlist)
				$fieldlist = substr($fieldlist, 1);
		
		
			$querystatement = "
				SELECT
				".$fieldlist."
				FROM
				`".$this->maintable."`
				WHERE
				".$whereclause;
		
			$queryresult = $this->db->query($querystatement);
		
			if($this->db->numRows($queryresult))
				$therecord = $this->db->fetchArray($queryresult);
			else
				$therecord = $this-> getDefaults();

			$therecord["catschanged"] = 1;
			$therecord["addcats"] = $this->getAdditionalStyleCategories($therecord["uuid"]);

			$therecord["categorieschanged"] = 1;
			$therecord["addcategories"] = $this->getAdditionalCategories($therecord["uuid"]);

			$therecord["sportschanged"] = 1;
			$therecord["addsports"] = $this->getAdditionalSports($therecord["uuid"]);

			return $therecord;

		}//end function getRecord


		function populateStyleArray(){

			// I need id as well to let updates work with our verify function
			// i.e. if its an update on existing record, its ok if the stylenumber
			// is not unique iff its already associated to the record being updated
			$this->availableStyles = array();

			$querystatement = "
				SELECT
					`id`,
					`stylenumber`
				FROM
					`styles`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){

				while($therecord = $this->db->fetchArray($queryresult)){

					$stylenumber = $therecord["stylenumber"];
					$id = $therecord["id"];

					$this->availableStyles[$stylenumber]["id"] = $id;

				}//end while

			}//wndif

		}//end method --populateStyleArray--


		function verifyVariables($variables){

			//must have a stylenumber...table default is not enough
			if(isset($variables["stylenumber"])){

				//must have some sort of stylenumber
				if($variables["stylenumber"] !== "" || $variables["stylenumber"] !== NULL){

					if($this->availableStyles === NULL)
						$this->populateStyleArray();

					//can't have this stylenumber already chosen
					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					$tempstylenumber = $variables["stylenumber"];// using this because it looks ugly to put the brackets within brackets
					if( array_key_exists($variables["stylenumber"], $this->availableStyles) ){

						if( $this->availableStyles[$tempstylenumber]["id"] !== $tempid )
							$this->verifyErrors[] = "The `stylenumber` field must be a unique number.";

					}else{
						$this->availableStyles[$tempstylenumber]["id"] = "aoihweoighaow giuahrweughauerhgaiudsf iaheiugaiuweg iagweiuha wiueg";// impossible id put in
					}//end if

				}else
					$this->verifyErrors[] = "The `stylenumber` field must not be blank.";

			}else
				$this->verifyErrors[] = "The `stylenumber` field must be set.";

			//must have a stylename
			if(isset($variables["stylename"])){
				//must have some sort of stylename
				if($variables["stylename"] !== "" || $variables["stylename"] !== NULL){
                                        //Any name validation needs to go here
				}else
					$this->verifyErrors[] = "The `stylename` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `stylename` field must be set.";

			if(isset($variables["status"])){

				switch($variables["status"]){

					case "In Stock":
					case "Out of Stock":
					case "Backordered":
						break;

					default:
						$this->verifyErrors[] = "The value of the `status` field is invalid.
							It must be 'In Stock', 'Out of Stock', or 'Backordered'.";
						break;

				}//end switch

			}//end if


			if(isset($variables["type"])){

				switch($variables["type"]){

					case "Inventory":
					case "Non-Inventory":
					case "Service":
					case "Kit":
					case "Assembly":
						break;

					default:
						$this->verifyErrors[] = "The value of the `type` field is invalid.
							It must be 'Inventory', 'Non-Inventory', 'Service', 'Kit', or 'Assembly'.";
						break;

				}//end switch

			}//end if

			if(isset($variables["supplierid"])){
				if($variables["supplierid"] == "")
					$this->verifyErrors[] = "The `supplier` field must be completed.";
			}//end if

			//check boolean
			if(isset($variables["webenabled"]))
				if($variables["webenabled"] && $variables["webenabled"] != 1)
					$this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["isoversized"]))
				if($variables["isoversized"] && $variables["isoversized"] != 1)
					$this->verifyErrors[] = "The `isoversized` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["isprepackaged"]))
				if($variables["isprepackaged"] && $variables["isprepackaged"] != 1)
					$this->verifyErrors[] = "The `isprepackaged` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["taxable"]))
				if($variables["taxable"] && $variables["taxable"] != 1)
					$this->verifyErrors[] = "The `taxable` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["inc_brighton"]))
				if($variables["inc_brighton"] && $variables["inc_brighton"] != 1)
					$this->verifyErrors[] = "The `inc. brighton on web` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function _commonPrepareVariables($variables){

                        $variables["inactive"] = ($variables["inactive"]==true ? 1 : 0);
                        $variables["webenabled"] = ($variables["webenabled"]==true ? 1 : 0);
                        $variables["taxable"] = ($variables["taxable"]==true ? 1 : 0);
                        $variables["inc_brighton"] = ($variables["inc_brighton"]==true ? 1 : 0);

                        if(!isset($variables["unitprice"]))
				$variables["thumchange"] = 0;

			if(!isset($variables["unitcost"]))
				$variables["thumchange"] = 0;

			if(isset($variables["saleprice"])) $variables["saleprice"] = currencyToNumber($variables["saleprice"]);
			if(isset($variables["unitprice"])) $variables["unitprice"] = currencyToNumber($variables["unitprice"]);
			if(isset($variables["unitcost"])) $variables["unitcost"] = currencyToNumber($variables["unitcost"]);

			if(!isset($variables["thumbchange"]))
				$variables["thumbchange"] = NULL;

			if($variables["thumbchange"]){

				if($variables["thumbchange"] == "upload"){
					$variables["thumbnail"] = $this->getPicture("thumbnailupload");
//					$variables["thumbnailmime"] = $_FILES['thumbnailupload']['type'];
				} else {
					//delete
					$variables["thumbnail"] = NULL;
//					$variables["thumbnailmime"] = NULL;
				}

			} // end thumbnail picture change if


			if(!isset($variables["picturechange"]))
				$variables["picturechange"] = NULL;

			if($variables["picturechange"]){

				if($variables["picturechange"] == "upload"){
					$variables["picture"] = $this->getPicture("pictureupload");
//					$variables["picturemime"] = $_FILES['pictureupload']['type'];
				} else {
					//delete
					$variables["picture"] = NULL;
//					$variables["picturemime"] = NULL;
				}

			}//end main picture change if

			if(!isset($variables["addcats"]))
				$variables["addcats"] = array();

			if(isset($variables["catschanged"]) && $variables["catschanged"]) {
				$variables["addcats"] = stripslashes($variables["addcats"]);
				$variables["addcats"] = json_decode($variables["addcats"], true);
			}//end if

			if(!isset($variables["addcategories"]))
				$variables["addcategories"] = array();

			if(isset($variables["categorieschanged"]) && $variables["categorieschanged"]) {
				$variables["addcategories"] = stripslashes($variables["addcategories"]);
				$variables["addcategories"] = json_decode($variables["addcategories"], true);
			}//end if

			if(!isset($variables["addsports"]))
				$variables["addsports"] = array();

			if(isset($variables["sportschanged"]) && $variables["sportschanged"]) {
				$variables["addsports"] = stripslashes($variables["addsports"]);
				$variables["addsports"] = json_decode($variables["addsports"], true);
			}//end if

			return $variables;

		}//end method --_commonPrepareVariables--


		function prepareVariables($variables){

			switch($variables["id"]){

				case "":
				case NULL:
				case 0:
					if(!hasRights("role:8400c7ae-af9f-657b-456b-9a1e9c2f1ef4")){
						unset($this->fields["stylenumber"]);
						unset($this->fields["stylename"]);
//						unset($this->fields["upc"]);
						unset($this->fields["description"]);
						unset($this->fields["inactive"]);
						unset($this->fields["taxable"]);
						unset($this->fields["inc_brighton"]);
						unset($this->fields["unitprice"]);
						unset($this->fields["unitcost"]);
						unset($this->fields["unitofmeasure"]);
						unset($this->fields["type"]);
						unset($this->fields["categoryid"]);
						unset($this->fields["default_categoryid"]);
						unset($this->fields["default_sportid"]);
						unset($this->fields["supplierid"]);

						unset($this->fields["webenabled"]);
						unset($this->fields["keywords"]);
						unset($this->fields["webdescription"]);

					} else {

						//user has rights.  Let's format everything.
						$variables = $this->_commonPrepareVariables($variables);

					}//end if

					if($variables["packagesperitem"])
						$variables["packagesperitem"]=1/$variables["packagesperitem"];

					break;

				default:
					$variables = $this->_commonPrepareVariables($variables);
					if(isset($variables["packagesperitem"]))
						if($variables["packagesperitem"])
							$variables["packagesperitem"] = 1 / $variables["packagesperitem"];
					break;

			}//end switch

			return $variables;

		}//end function prepareVariables

		/**
		 * Overriden phpbmstable function
		 */
		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			parent::updateRecord($variables, $modifiedby, $useUuid);

			if(isset($variables["catschanged"]) && $variables["catschanged"])
				$this->updateStyleCategories($variables["uuid"], $variables["addcats"]);

			if(isset($variables["categorieschanged"]) && $variables["categorieschanged"])
				$this->updateCategories($variables["uuid"], $variables["addcategories"]);

			if(isset($variables["sportschanged"]) && $variables["sportschanged"])
				$this->updateSports($variables["uuid"], $variables["addsports"]);

			//need to reset the field information.  If they did not have rights
			// we temporarilly removed the fields to be updated.
			$this->getTableInfo();

                        //Ensure the stock figures are updated (in case we amended the inc_brighton field)
                        //Update available quantity
                        $querystatement='UPDATE `products` p JOIN `styles` s ON (p.`styleid`=s.`uuid`)
                                            SET p.`available_stock`=(p.`bleep_webstore`+p.`bleep_whse`+(p.`bleep_brighton`*s.`inc_brighton`))
                                          WHERE s.`uuid` ="'.$variables["uuid"].'";';
                        $queryresult = $this->db->query($querystatement);
                        if(!$queryresult){
                                $error= new appError(0,"Could Not Update Quantities at Product Level","STYLE UPDATE");
                        }
                        //Update available quantity at Style Level
                        $querystatement='UPDATE styles SET available_stock = (SELECT SUM(available_stock) FROM products WHERE products.styleid = styles.uuid)
                                          WHERE `uuid` ="'.$variables["uuid"].'";';
                        $queryresult = $this->db->query($querystatement);
                        if(!$queryresult){
                                $error= new appError(0,"Could Not Update Quantities at Product Level","STYLE UPDATE");
                        }

		}//end function updateRecord


		/**
		 * Overriden phpbmstable function
		 */
		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($createdby === NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			if(is_array($newid))
				$uuid = $newid["uuid"];
			else
				$uuid = $variables["uuid"];

                        if(isset($variables["catschanged"])) //will only be set if using manual inputs (not import)
                        {
                            if($variables["catschanged"])
                                    $this->updateStyleCategories($uuid, $variables["addcats"]);
                        }

                        if(isset($variables["categorieschanged"]))
                        {
                            if($variables["categorieschanged"])
                                    $this->updateCategories($uuid, $variables["addcategories"]);
                        }

                        if(isset($variables["sportschanged"]))
                        {
                            if($variables["sportschanged"])
                                    $this->updateSports($uuid, $variables["addsports"]);
                        }

			return $newid;

		}//end function insertRecord


		/**
		* Retrieves and displays a list of possible style categories
		*
		* @param string $categoryid style category uuid
		*/
		function displayStyleCategories($categoryid){

            		$categoryid = mysql_real_escape_string($categoryid);

			$querystatement = "
				SELECT
					`uuid`,
					`name`
				FROM
					`stylecategories`
				WHERE
					`inactive` = 0 OR `uuid` ='".$categoryid."'
				ORDER BY
					`name`
				";

			$queryresult = $this->db->query($querystatement);

			?>
				<select name="categoryid" id="categoryid">
						<option value="" <?php if($categoryid=="") echo 'selected="selected"'?>>No Master Category</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){

						?>
						<option value="<?php echo $therecord["uuid"]?>" <?php if($categoryid==$therecord["uuid"]) echo 'selected="selected"' ?>><?php echo $therecord["name"];?></option>
						<?php

					}//endwhile
				?>
			</select>
            <?php

		}//end function displayStyleCategories


		/**
		 * function getAdditionalStyleCategories
		 * @param $uuid
		 *
		 * @return array Array of category records
		 */

		function getAdditionalStyleCategories($uuid) {

			$thereturn = array();

			$querystatement = "
				SELECT
					stylecategories.uuid AS catid,
					stylecategories.name,
					stylecategories.uuid AS `stylecategoryid`
				FROM
					(styles INNER JOIN stylestostylecategories ON styles.uuid = stylestostylecategories.styleid)
					INNER JOIN stylecategories ON stylestostylecategories.stylecategoryid = stylecategories.uuid
				WHERE
					styles.uuid = '".$uuid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				while($therecord = $this->db->fetchArray($queryresult))
					$thereturn[] = $therecord;

			return $thereturn;

		}//end if


		/**
		 * displays a list of additional categories associated with the style.
		 *
		 * @param array $categoryArray array of category records
		 *
		 */
		function displayAdditionalStyleCategories($categoryArray){

			?>
			<div id="catDiv">
				<input type="hidden" id="addcats" name="addcats" value="" />
				<input type="hidden" id="catschanged" name="catschanged" value="0" />
			<?php

			$i = 0;

			foreach($categoryArray as $therecord){

				?>
				<div class="moreCats" id="AC<?php echo $i; ?>">
					<input type="text" value="<?php echo formatVariable($therecord["name"]); ?>" id="AC-<?php echo $i ?>" size="30" readonly="readonly"/>
					<input type="hidden" id="AC-CatId-<?php echo $i ?>" value="<?php echo $therecord["catid"];?>" class="catIDs"/>
					<button type="button" class="graphicButtons buttonMinus catButtons" title="Remove Category"><span>-</span></button>
				</div>
				<?php

				$i++;

			}//endwhile

			?></div><?php

		}//end function displayAdditionalStyleCategories


		/**
		 * updates additional categories for style (by wiping current list and adding new ones)
		 *
		 * @param string $recorduuid style's uuid
		 * @param string $categoryList comma separated list of style category uuids
		 *
		 */
		function updateStyleCategories($recorduuid, $categoryList){

			//first remove any existing records
			$deletestatement = "
				DELETE FROM
					`stylestostylecategories`
				WHERE
					`styleid` = '".$recorduuid."'
				";

			$this->db->query($deletestatement);

			foreach($categoryList as $item){

				$insertstatement = "
					INSERT INTO
						`stylestostylecategories`
						(styleid, stylecategoryid)
					VALUES
						(
						'".$recorduuid."',
						'".$item["stylecategoryid"]."'
						)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end function updateStyleCategories

		/**
		* Retrieves and displays a list of possible style categories
		*
		* @param string $categoryid style category uuid
		*/
		function displayCategories($categoryid){

            		$categoryid = mysql_real_escape_string($categoryid);

			$querystatement = "
				SELECT
					`uuid`,
					`name`
				FROM
					`categories`
				WHERE
					`inactive` = 0 OR `uuid` ='".$categoryid."'
				ORDER BY
					`name`
				";

			$queryresult = $this->db->query($querystatement);

			?>
				<select name="categoryid" id="categoryid">
						<option value="" <?php if($categoryid=="") echo 'selected="selected"'?>>No Master Category</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){

						?>
						<option value="<?php echo $therecord["uuid"]?>" <?php if($categoryid==$therecord["uuid"]) echo 'selected="selected"' ?>><?php echo $therecord["name"];?></option>
						<?php

					}//endwhile
				?>
			</select>
            <?php

		}//end function displayCategories


		/**
		 * function getAdditionalCategories
		 * @param $uuid
		 *
		 * @return array Array of category records
		 */

		function getAdditionalCategories($uuid) {

			$thereturn = array();

			$querystatement = "
				SELECT
					categories.uuid AS categoryid,
					categories.name,
					categories.uuid AS `stylecategoryid`
				FROM
					(styles INNER JOIN stylestocategories ON styles.uuid = stylestocategories.styleid)
					INNER JOIN categories ON stylestocategories.categoryid = categories.uuid
				WHERE
					styles.uuid = '".$uuid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				while($therecord = $this->db->fetchArray($queryresult))
					$thereturn[] = $therecord;

			return $thereturn;

		}//end if


		/**
		 * displays a list of additional categories associated with the style.
		 *
		 * @param array $categoryArray array of category records
		 *
		 */
		function displayAdditionalCategories($categoryArray){

			?>
			<div id="categoryDiv">
				<input type="hidden" id="addcategories" name="addcategories" value="" />
				<input type="hidden" id="categorieschanged" name="categorieschanged" value="0" />
			<?php

			$i = 0;

			foreach($categoryArray as $therecord){

				?>
				<div class="moreCategories" id="AC<?php echo $i; ?>">
					<input type="text" value="<?php echo formatVariable($therecord["name"]); ?>" id="AC-<?php echo $i ?>" size="30" readonly="readonly"/>
					<input type="hidden" id="AC-CategoryId-<?php echo $i ?>" value="<?php echo $therecord["categoryid"];?>" class="categoryIDs"/>
					<button type="button" class="graphicButtons buttonMinus categoryButtons" title="Remove Category"><span>-</span></button>
				</div>
				<?php

				$i++;

			}//endwhile

			?></div><?php

		}//end function displayAdditionalCategories


		/**
		 * updates additional categories for style (by wiping current list and adding new ones)
		 *
		 * @param string $recorduuid style's uuid
		 * @param string $categoryList comma separated list of style category uuids
		 *
		 */
		function updateCategories($recorduuid, $categoryList){

			//first remove any existing records
			$deletestatement = "
				DELETE FROM
					`stylestocategories`
				WHERE
					`styleid` = '".$recorduuid."'
				";

			$this->db->query($deletestatement);

			foreach($categoryList as $item){

				$insertstatement = "
					INSERT INTO
						`stylestocategories`
						(styleid, categoryid)
					VALUES
						(
						'".$recorduuid."',
						'".$item["categoryid"]."'
						)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end function updateCategories

		/**
		* Retrieves and displays a list of possible style sports
		*
		* @param string $sportid style sport uuid
		*/
		function displayStyleSports($sportid){

            		$sportid = mysql_real_escape_string($sportid);

			$querystatement = "
				SELECT
					`uuid`,
					`name`
				FROM
					`sports`
				WHERE
					`inactive` = 0 OR `uuid` ='".$sportid."'
				ORDER BY
					`name`
				";

			$queryresult = $this->db->query($querystatement);

			?>
				<select name="sportid" id="sportid">
						<option value="" <?php if($sportid=="") echo 'selected="selected"'?>>No Master Sport</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){

						?>
						<option value="<?php echo $therecord["uuid"]?>" <?php if($sportid==$therecord["uuid"]) echo 'selected="selected"' ?>><?php echo $therecord["name"];?></option>
						<?php

					}//endwhile
				?>
			</select>
            <?php

		}//end function displayStyleSports


		/**
		 * function getAdditionalSports
		 * @param $uuid
		 *
		 * @return array Array of sport records
		 */

		function getAdditionalSports($uuid) {

			$thereturn = array();

			$querystatement = "
				SELECT
					sports.uuid AS sportid,
					sports.name,
					sports.uuid AS `stylesportid`
				FROM
					(styles INNER JOIN stylestosports ON styles.uuid = stylestosports.styleid)
					INNER JOIN sports ON stylestosports.sportid = sports.uuid
				WHERE
					styles.uuid = '".$uuid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				while($therecord = $this->db->fetchArray($queryresult))
					$thereturn[] = $therecord;

			return $thereturn;

		}//end if


		/**
		 * displays a list of additional sports associated with the style.
		 *
		 * @param array $sportArray array of sport records
		 *
		 */
		function displayAdditionalSports($sportArray){

			?>
			<div id="sportDiv">
				<input type="hidden" id="addsports" name="addsports" value="" />
				<input type="hidden" id="sportschanged" name="sportschanged" value="0" />
			<?php

			$i = 0;

			foreach($sportArray as $therecord){

				?>
				<div class="moreSports" id="AS<?php echo $i; ?>">
					<input type="text" value="<?php echo formatVariable($therecord["name"]); ?>" id="AS-<?php echo $i ?>" size="30" readonly="readonly"/>
					<input type="hidden" id="AS-SportId-<?php echo $i ?>" value="<?php echo $therecord["sportid"];?>" class="sportIDs"/>
					<button type="button" class="graphicButtons buttonMinus sportButtons" title="Remove Sport"><span>-</span></button>
				</div>
				<?php

				$i++;

			}//endwhile

			?></div><?php

		}//end function displayAdditionalSports


		/**
		 * updates additional sports for style (by wiping current list and adding new ones)
		 *
		 * @param string $recorduuid style's uuid
		 * @param string $sportList comma separated list of style sport uuids
		 *
		 */
		function updateSports($recorduuid, $sportList){

			//first remove any existing records
			$deletestatement = "
				DELETE FROM
					`stylestosports`
				WHERE
					`styleid` = '".$recorduuid."'
				";

			$this->db->query($deletestatement);

			foreach($sportList as $item){

				$insertstatement = "
					INSERT INTO
						`stylestosports`
						(styleid, sportid)
					VALUES
						(
						'".$recorduuid."',
						'".$item["sportid"]."'
						)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end function updateSports
		
		/*
		 * function api_searchByStyleNumber
		 * @param array $requestData Array containing the "stylenumber" key.
		 * @param bool $returnUuid If true, returns result's uuid , if
		 * false, the id.
		 * @return array An array containing response information
		 * @returnf string 'type' The type of response (e.g. 'error' or 'result')
		 * @returnf string 'message' Message explaining the type / result
		 * @returnf array details Either the array of uuid / ids if no errors
		 * were encountered, or the original $requestData if there was an error
		 */
		
		function api_searchByStyleNumber($requestData, $returnUuid = true) {
			
			/**
			  *  do error search 
			  */
			if(!isset($requestData["stylenumber"])){
				$response["type"] = "error";
				$response["message"] = "Data does not contain a key of 'stylenumber'.";
				$response["details"] = $requestData;
				return $response;
			}//end if
			
			/**
			  *  do query search 
			  */
			$querystatement = "
				SELECT
					`id`,
					`uuid`
				FROM
					`styles`
				WHERE
					`stylenumber` = '".mysql_real_escape_string($requestData["stylenumber"])."'
			";
			
			$queryresult = $this->db->query($querystatement);
			
			/**
			  *  report result 
			  */
			$thereturn["message"] = "The function api_searchByStyleNumber has been run successfully.";
			$thereturn["type"] = "";
			$thereturn["details"] = array();
			while($therecord = $this->db->fetchArray($queryresult)){
				
				if($returnUuid)
					$thereturn["details"][] = $therecord["uuid"];
				else
					$thereturn["details"][] = $therecord["id"];
					
			}//end while
			
			return $thereturn;
			
		}//end function --api_searchByStyleNumber--

		/**
		 * automatically translates the text fields for the stated lanugage
		 *
		 * @param string $language
		 *
		 */
		function updateTranslation($language=""){

echo "step 4a\n";
			//if we do not have a valid style then exit
			if(!isset($therecord["uuid"])) return false;
			if(!$therecord["uuid"]) return false;

echo "step 4b\n";
//			switch strtoupper($language){
//				case "FRENCH":
//					$iso="fr";
//					return false;
//				default:
//					echo "Invalid Language (".$language.") Passed";
//					return false;
//			}

echo "step 4c\n";
//			require_once("google_translate.php");
echo "step 4d\n";








			return true;

			//first remove any existing records
			$deletestatement = "
				DELETE FROM
					`stylestostylecategories`
				WHERE
					`styleid` = '".$recorduuid."'
				";

			$this->db->query($deletestatement);

			foreach($categoryList as $item){

				$insertstatement = "
					INSERT INTO
						`stylestostylecategories`
						(styleid, stylecategoryid)
					VALUES
						(
						'".$recorduuid."',
						'".$item["stylecategoryid"]."'
						)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end function updateStyleCategories

	}//end class styles

}//end if

if(class_exists("searchFunctions")){
	class stylesSearchFunctions extends searchFunctions{

                function imagesImport($useUUID = false){
                    goURL(APP_PATH."modules/activewms/images_import.php?id=tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
                }

                function priceImport($useUUID = false){
                    goURL(APP_PATH."modules/activewms/price_import.php?id=tbld:7ecb8e4e-8301-11df-b557-00238b586e42");
                }

                /**
		* deletes style/ marks as inactive
		*/
		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause("styles.id");
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement = "
				UPDATE `styles`
                                SET `inactive` = 1
				WHERE (".$whereclause.")
			";

			$queryresult = $this->db->query($querystatement);

//			$message = $this->buildStatusMessage();
//			$message.=" set to inactive.";
			$message ="Record(s) set as inactive.";

			return $message;

		}
	}//end class
}//end if

if(class_exists("phpbmsImport")){
	class stylesImport extends phpbmsImport{

		function importRecords($rows, $titles){

			switch($this->importType){

				case "csv":
                                    //count total fieldnames (top row of csv document)
                                    $fieldNum = count($titles);

                                    //$columns array maps required vars to column no's
                                    $columns=array();
                                    foreach($titles AS $col=>$value){
                                        switch(strtoupper($value)){
                                            case "STYLE CODE":
                                            case "STYLENUMBER":
                                                $columns["stylenumber"]=$value;
                                                break;
                                            case "STYLE NAME":
                                            case "STYLENAME":
                                                $columns["stylename"]=$value;
                                                break;
                                            case "BRAND":
                                            case "SUPPLIER":
                                                $columns["supplierid"]=$value;
                                                break;
                                            case "DESCRIPTION":
                                                $columns["description"]=$value;
                                                break;
                                            case "ENGLISH DESCRIPTION":
                                            case "WEB DESCRIPTION":
                                                $columns["webdescription"]=$value;
                                                break;
                                            case "SPORT":
                                            case "DEFAULT SPORT":
                                                $columns["default_sportid"]=$value;
                                                break;
                                            case "CATEGORY":
                                            case "DEFAULT CATEGORY":
                                                $columns["default_categoryid"]=$value;
                                                break;
                                            case "SEASON":
                                                $columns["season"]=$value;
                                                break;
                                        }
                                    }

                                    //Verify required column names are present
                                    $checkRecords = true;
                                    if(!isset($columns["stylenumber"])){
                                        $this->error .= '<li> Required column missing [Style Code]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["stylename"])){
                                        $this->error .= '<li> Required column missing [Style Name]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["supplierid"])){
                                        $this->error .= '<li> Required column missing [Brand]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["description"])){
                                        $this->error .= '<li> Required column missing [Description]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["webdescription"])){
                                        $this->error .= '<li> Required column missing [Web Description]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["season"])){
                                        $this->error .= '<li> Required column missing [Season]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!$checkRecords)
                                        //Do not bother to check the records of the titles are wrong.
                                        break;

                                    //the file starts at line number 1, but since line 1 is
                                    //supposed to be the fieldnames in the table(s), the lines
                                    //being insereted start @ 2.
                                    $rowNum = 2;

                                    //get the data one row at a time
                                    foreach($rows as $rowData){

                                            $theid = 0; // set for when verifification does not pass
                                            $verify = array(); //set for when number of field rows does not match number of titles

                                            //trim off leading/trailing spaces
                                            $trimmedRowData = array();
                                            $categoryidList = array();
                                            $sportidList = array();

//                                            foreach($rowData as $name => $data)
//                                                $trimmedRowData[$name] = trim($data);
                                            foreach($columns as $name => $key){
                                                switch ($name) {
                                                    case "supplierid":
                                                        $querystatement = "SELECT `uuid` FROM `suppliers` WHERE `name` LIKE '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]))
                                                            $this->error .= '<li> unknown Brand ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        break;
                                                    case "default_categoryid":
                                                        $querystatement = "SELECT `uuid` FROM `categories` WHERE `name` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]) && trim($rowData[$key]!=""))
                                                            $this->error .= '<li> unknown Category ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        //add default category to list of categories
                                                        $addCategory=array();
                                                        $addCategory['categoryid'] = $therecord["uuid"];
                                                        $categoryidList[] = $addCategory;
                                                        break;
                                                    case "default_sportid":
                                                        $sports = explode(',', trim($rowData[$key]));
                                                        foreach($sports as $sport){
                                                            $querystatement = "SELECT `uuid` FROM `sports` WHERE `name` = '".trim($sport)."' LIMIT 1;";
                                                            $queryresult = $this->table->db->query($querystatement);
                                                            $therecord = $this->table->db->fetchArray($queryresult);
                                                            if(!isset($therecord["uuid"]) && trim($sport!=""))
                                                                $this->error .= '<li> unknown Sport ['.trim($sport).'] for line number '.$rowNum.'.</li>';
                                                            //Only set the default sport for the first item
                                                            if(!isset($trimmedRowData[$name]))
                                                                $trimmedRowData[$name] = $therecord["uuid"];
                                                            //add default sports to list of sports
                                                            $addSport=array();
                                                            $addSport['sportid'] = $therecord["uuid"];
                                                            $sportidList[] = $addSport;
                                                        }
                                                        break;
                                                    default:
                                                        $trimmedRowData[$name] = trim($rowData[$key]);
                                                }
                                            }
                                            if(count($categoryidList)>0){
                                                $trimmedRowData['categorieschanged']=true;
                                                $trimmedRowData['addcategories']=$categoryidList;
                                            }
                                            if(count($sportidList)>0){
                                                $trimmedRowData['sportschanged']=true;
                                                $trimmedRowData['addsports']=$sportidList;
                                            }

                                            //check to see if number of fieldnames is consistent for each row
                                            $rowFieldNum = count($trimmedRowData);

                                            //if valid, insert, if not, log error and don't insert.
//                                            if($rowFieldNum == $fieldNum){
                                                    $verify = $this->table->verifyVariables($trimmedRowData);
                                                    if(!count($verify)){
                                                            $createdby = NULL;
                                                            $overrideID = true;
                                                            $replace = false;
                                                            if(!isset($trimmedRowData["uuid"])){
                                                                    $useUuid = true;
                                                                    $thereturn = $this->table->insertRecord($trimmedRowData, $createdby, $overrideID, $replace, $useUuid);
                                                                    $theid = $thereturn["id"];
                                                            }else{
                                                                    $useUuid = false;
                                                                    $thereturn = $this->table->insertRecord($trimmedRowData, $createdby, $overrideID, $replace, $useUuid);
                                                                    $theid = $thereturn;
                                                            }
                                                    }//end if
//                                            }else
//                                                    $this->error .= '<li> incorrect amount of fields for line number '.$rowNum.'.</li>';

                                            if($theid){
                                                    //keep track of the ids in the transaction to be able to select them
                                                    //for preview purposes
                                                    $this->transactionIDs[] = $theid;

                                                    //get first id to correct auto increment
                                                    if(!$this->revertID)
                                                            $this->revertID = $theid;
                                            }else
                                                    $this->error .= '<li> failed insert for line number '.$rowNum.'.</li>';

                                            foreach($verify as $error)
                                                    $this->error .= '<li class="subError">'.$error.'</li>';

                                            $rowNum++;

                                    }//end foreach
				break;

			}//end switch

		}//end method --importRecords--
	}//end class

	class stylesPriceImport extends phpbmsImport{

		function importRecords($rows, $titles){

			switch($this->importType){

				case "csv":
                                    //count total fieldnames (top row of csv document)
                                    $fieldNum = count($titles);

                                    //$columns array maps required vars to column no's
                                    $columns=array();
                                    foreach($titles AS $col=>$value){
                                        switch(trim(strtoupper($value))){
                                            case "STYLE CODE":
                                            case "OUR STYLE CODE":
                                            case "STYLE NUMBER":
                                            case "STYLENUMBER":
                                                $columns["uuid"]=$value;
                                                break;
                                            case "COST @ DISCOUNT":
                                            case "COST PRICE":
                                            case "UNIT COST":
                                            case "UNITCOST":
                                                $columns["unitcost"]=$value;
                                                break;
                                            case "RETAIL":
                                            case "RETAIL PRICE":
                                            case "UNIT PRICE":
                                            case "UNITPRICE":
                                                $columns["unitprice"]=$value;
                                                break;
                                            case "PROMO":
                                            case "PROMO PRICE":
                                            case "SALE":
                                            case "SALE PRICE":
                                            case "SALEPRICE":
                                                $columns["saleprice"]=$value;
                                                break;
                                        }
                                    }

                                    //Verify required column names are present
                                    $checkRecords = true;
                                    if(!isset($columns["uuid"])){
                                        $this->error .= '<li> Required column missing [Style Code] / [Our Style Code] / [Style Number] / [StyleNumber]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["unitcost"])){
                                        $this->error .= '<li> Required column missing [COST PRICE] / [COST @ DISCOUNT] / [UNIT COST] / [UNITCOST]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["unitprice"])){
                                        $this->error .= '<li> Required column missing [RETAIL] / [RETAIL PRICE] / [UNIT PRICE] / [UNITPRICE]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["saleprice"])){
                                        $this->error .= '<li> Required column missing [SALE] / [SALE PRICE] / [SALEPRICE]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!$checkRecords)
                                        //Do not bother to check the records of the titles are wrong.
                                        break;

                                    //the file starts at line number 1, but since line 1 is
                                    //supposed to be the fieldnames in the table(s), the lines
                                    //being insereted start @ 2.
                                    $rowNum = 2;

                                    //get the data one row at a time
                                    foreach($rows as $rowData){

                                            $theid = 0; // set for when verifification does not pass
                                            $verify = array(); //set for when number of field rows does not match number of titles

                                            //trim off leading/trailing spaces
                                            $trimmedRowData = array();

//                                            foreach($rowData as $name => $data)
//                                                $trimmedRowData[$name] = trim($data);
                                            foreach($columns as $name => $key){
                                                switch ($name) {
                                                    case "uuid":
//                                                        $querystatement = "SELECT `uuid` FROM `styles` WHERE `stylename` = CONCAT('".trim($rowData[$key])."',' - ','".trim($rowData['colourid'])."') LIMIT 1;";
                                                        $querystatement = "SELECT `id`,`uuid`,`stylenumber`,`stylename` FROM `styles` WHERE `stylenumber` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]) && trim($rowData[$key]!=""))
//                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).' - '.trim($rowData['colourid']).'] for line number '.$rowNum.'.</li>';
                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        $trimmedRowData["stylenumber"] = $therecord["stylenumber"];
                                                        $trimmedRowData["stylename"] = $therecord["stylename"];
                                                        $trimmedRowData["id"] = $therecord["id"];
                                                        break;
                                                    case "unitcost":
                                                    case "unitprice":
                                                    case "saleprice":
                                                        $trimmedRowData[$name] = (float)trim($rowData[$key]);
                                                    default:
                                                        $trimmedRowData[$name] = trim($rowData[$key]);
                                                }
                                            }
                                            //If the sale price has been set to the same as the retail price
                                            // then reset it to 0. (i.e. no sale price)
                                            if($trimmedRowData["saleprice"]==$trimmedRowData["unitprice"])
                                                $trimmedRowData["saleprice"]=0;

                                            //check to see if number of fieldnames is consistent for each row
                                            $rowFieldNum = count($trimmedRowData);

                                            //if valid, insert, if not, log error and don't insert.
//                                            if($rowFieldNum == $fieldNum){
                                                    $verify = $this->table->verifyVariables($trimmedRowData);
                                                    if(!count($verify)){
                                                            $createdby = NULL;
                                                            if(!isset($trimmedRowData["uuid"])){
                                                                    $useUuid = true;
                                                                    $thereturn = $this->table->updateRecord($trimmedRowData, $createdby, $useUuid);
                                                                    $theid = $thereturn["id"];
                                                            }else{
                                                                    $useUuid = false;
                                                                    $thereturn = $this->table->updateRecord($trimmedRowData, $createdby, $useUuid);
                                                                    $theid = $thereturn;
                                                            }
                                                    }//end if
//                                            }else
//                                                    $this->error .= '<li> incorrect amount of fields for line number '.$rowNum.'.</li>';

//                                            if($theid){
                                                    //keep track of the ids in the transaction to be able to select them
                                                    //for preview purposes
//                                                    $this->transactionIDs[] = $theid;
                                                    $this->transactionIDs[] = $trimmedRowData["id"];

                                                    //get first id to correct auto increment
//                                                    if(!$this->revertID)
//                                                            $this->revertID = $theid;
//                                            }else
//                                                    $this->error .= '<li> failed update for line number '.$rowNum.'.</li>';

                                            foreach($verify as $error)
                                                    $this->error .= '<li class="subError">'.$error.'</li>';

                                            $rowNum++;

                                    }//end foreach
				break;

			}//end switch

		}//end method --importRecords--
	}//end class

	class stylesImagesImport extends phpbmsImport{

		function importRecords($rows, $titles){

			switch($this->importType){

				case "csv":
                                    //count total fieldnames (top row of csv document)
                                    $fieldNum = count($titles);

                                    //$columns array maps required vars to column no's
                                    $columns=array();
                                    foreach($titles AS $col=>$value){
                                        switch(trim(strtoupper($value))){
                                            case "STYLE CODE":
                                            case "OUR STYLE CODE":
                                            case "STYLE NUMBER":
                                            case "STYLENUMBER":
                                                $columns["uuid"]=$value;
                                                break;
                                            case "MAIN IMAGE":
                                            case "MAIN_IMAGE":
                                                $columns["main_image"]=$value;
                                                break;
                                            case "ALT IMAGE1":
                                            case "ALT_IMAGE1":
                                                $columns["alt_image1"]=$value;
                                                break;
                                            case "ALT IMAGE2":
                                            case "ALT_IMAGE2":
                                                $columns["alt_image2"]=$value;
                                                break;
                                            case "ALT IMAGE3":
                                            case "ALT_IMAGE3":
                                                $columns["alt_image3"]=$value;
                                                break;
                                            case "ALT IMAGE4":
                                            case "ALT_IMAGE4":
                                                $columns["alt_image4"]=$value;
                                                break;
                                        }
                                    }

                                    //Verify required column names are present
                                    $checkRecords = true;
                                    if(!isset($columns["uuid"])){
                                        $this->error .= '<li> Required column missing [Style Code] / [Our Style Code] / [Style Number] / [StyleNumber]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["main_image"]) && !isset($columns["alt_image1"]) && !isset($columns["alt_image2"]) && !isset($columns["alt_image3"]) && !isset($columns["alt_image4"])){
                                        $this->error .= '<li> At least one of the image columns needs to be present [MAIN IMAGE] / [ALT IMAGE1] / [ALT IMAGE2] / [ALT IMAGE3] / [ALT IMAGE4]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!$checkRecords)
                                        //Do not bother to check the records of the titles are wrong.
                                        break;

                                    //the file starts at line number 1, but since line 1 is
                                    //supposed to be the fieldnames in the table(s), the lines
                                    //being insereted start @ 2.
                                    $rowNum = 2;

                                    //get the data one row at a time
                                    foreach($rows as $rowData){

                                            $theid = 0; // set for when verifification does not pass
                                            $verify = array(); //set for when number of field rows does not match number of titles

                                            //trim off leading/trailing spaces
                                            $trimmedRowData = array();

//                                            foreach($rowData as $name => $data)
//                                                $trimmedRowData[$name] = trim($data);
                                            foreach($columns as $name => $key){
                                                switch ($name) {
                                                    case "uuid":
//                                                        $querystatement = "SELECT `uuid` FROM `styles` WHERE `stylename` = CONCAT('".trim($rowData[$key])."',' - ','".trim($rowData['colourid'])."') LIMIT 1;";
                                                        $querystatement = "SELECT `id`,`uuid`,`stylenumber`,`stylename` FROM `styles` WHERE `stylenumber` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]) && trim($rowData[$key]!=""))
//                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).' - '.trim($rowData['colourid']).'] for line number '.$rowNum.'.</li>';
                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        $trimmedRowData["stylenumber"] = $therecord["stylenumber"];
                                                        $trimmedRowData["stylename"] = $therecord["stylename"];
                                                        $trimmedRowData["id"] = $therecord["id"];
                                                        break;
                                                    case "main_image":
                                                    case "alt_image1":
                                                    case "alt_image2":
                                                    case "alt_image3":
                                                    case "alt_image4":
                                                        $trimmedRowData[$name] = trim($rowData[$key]);
                                                    default:
                                                        $trimmedRowData[$name] = trim($rowData[$key]);
                                                }
                                            }

                                            //check to see if number of fieldnames is consistent for each row
                                            $rowFieldNum = count($trimmedRowData);

                                            //if valid, insert, if not, log error and don't insert.
//                                            if($rowFieldNum == $fieldNum){
                                                    $verify = $this->table->verifyVariables($trimmedRowData);
                                                    if(!count($verify)){
                                                            $createdby = NULL;
                                                            if(!isset($trimmedRowData["uuid"])){
                                                                    $useUuid = true;
                                                                    $thereturn = $this->table->updateRecord($trimmedRowData, $createdby, $useUuid);
                                                                    $theid = $thereturn["id"];
                                                            }else{
                                                                    $useUuid = false;
                                                                    $thereturn = $this->table->updateRecord($trimmedRowData, $createdby, $useUuid);
                                                                    $theid = $thereturn;
                                                            }
                                                    }//end if
//                                            }else
//                                                    $this->error .= '<li> incorrect amount of fields for line number '.$rowNum.'.</li>';

//                                            if($theid){
                                                    //keep track of the ids in the transaction to be able to select them
                                                    //for preview purposes
//                                                    $this->transactionIDs[] = $theid;
                                                    $this->transactionIDs[] = $trimmedRowData["id"];

                                                    //get first id to correct auto increment
//                                                    if(!$this->revertID)
//                                                            $this->revertID = $theid;
//                                            }else
//                                                    $this->error .= '<li> failed update for line number '.$rowNum.'.</li>';

                                            foreach($verify as $error)
                                                    $this->error .= '<li class="subError">'.$error.'</li>';

                                            $rowNum++;

                                    }//end foreach
				break;

			}//end switch

		}//end method --importRecords--
	}//end class
}//end if

?>
