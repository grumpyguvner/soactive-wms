<?php

if(class_exists("addresses")){
	class addresstorecord extends addresses{

		var $_availableTabledefUUIDs = NULL;
		var $_availableTabledefNames = NULL;

		function createAddressToRecord($variables, $addressid, $createdby = NULL){
			// This functions adds the addresstorecord record tying the client and address
			// records.

			if(!$createdby && isset($_SESSION["userinfo"]["id"]))
				$createdby = $_SESSION["userinfo"]["id"];

			$insertstatement = "
				INSERT INTO
					addresstorecord
				(
					tabledefid,
					recordid,
					defaultshipto,
					`primary`,
					addressid,
					createdby,
					creationdate,
					modifiedby,
					modifieddate
				) VALUES (
					'".mysql_real_escape_string($variables["tabledefid"])."',
					'".mysql_real_escape_string($variables["recordid"])."',
					".((int) $variables["defaultshipto"]).",
					".((int) $variables["primary"]).",
					'".mysql_real_escape_string($addressid)."',
					".((int) $createdby).",
					NOW(),
					".((int) $createdby).",
					NOW()
				)";

			$insertresult = $this->db->query($insertstatement);

			if($insertresult)
				return $this->db->insertId();
			else
				return false;

		}//end method - createAddressToRecord

		// CLASS OVERRIDES ===============================================
		// ===============================================================
		function getRecord($id, $useUuid = false){

                        if($useUuid){

                            $id = mysql_real_escape_string($id);
                            $whereField = "uuid";

                        } else {

                            $id = (int) $id;
                            $whereField = "id";

                        }//endif

			$querystatement = "
				SELECT
					*
				FROM
					addresstorecord
				WHERE
					`".$whereField."` = ".$id;

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)) {

				$therecord = $this->db->fetchArray($queryresult);

				$querystatement = "
					SELECT
						`id` AS `addressid`
					FROM
						`addresses`
					WHERE
						`uuid`='".mysql_real_escape_string($therecord["addressid"])."'
				";

				$queryresult = $this->db->query($querystatement);

                        	if($this->db->numRows($queryresult)){

					$addressID = $this->db->fetchArray($queryresult);
					$therecord["addressuuid"] = $therecord["addressid"]; //artificial uuid field... maybe bad
					$therecord["addressid"] = $addressID["addressid"];

					$addressrecord = parent::getRecord($therecord["addressid"]);

					unset($therecord["id"], $addressrecord["createdby"], $addressrecord["creationdate"], $addressrecord["modifiedby"], $addressrecord["modifieddate"]);

					$therecord = array_merge($addressrecord, $therecord);

				} else
					$therecord = $this->getDefaults();

			} else
				$therecord = $this->getDefaults();

			return $therecord;

		}//end method - getRecord


		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["addressid"] = 0;
			$therecord["addressuuid"] = ""; // may not be the best way
			$therecord["tabledefid"] = "";
			$therecord["recordid"] = "";
			$therecord["defaultshipto"] = 0;
			$therecord["primary"] = 0;

			return $therecord;

		}//end method - getDefauls


		function populateTabledefArrays(){

			$this->_availableTabledefUUIDs = array();
			$this->_availableTabledefNames = array();

			$querystatement = "
				SELECT
					`uuid`,
					`maintable`
				FROM
					`tabledefs`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult)){
					$this->_availableTabledefUUIDs[] = $therecord["uuid"];
					$this->_availableTabledefNames[$therecord["uuid"]] = $therecord["maintable"];
				}//end while
			}//end if

		}//end method --populateTabledefArray--


		function checkRecordID($recordid, $tablename){

			$recordid = mysql_real_escape_string($recordid);
			$tablename = mysql_real_escape_string($tablename);

			$querystatement = "
				SELECT
					`id`
				FROM
					`".$tablename."`
				WHERE
					`uuid` = '".$recordid."';
				";

			$queryresult = $this->db->query($querystatement);

			return $this->db->numRows($queryresult);

		}//end method --checkRecordID--


		function verifyVariables($variables){
			//POSSIBLY CHANGE>>>> NOT FINISHIED >>>>
			//Check tabledefs
			if(isset($variables["tabledefid"])){

				if($this->_availableTabledefUUIDs === NULL || $this->_availableTabledefNames === NULL)
					$this->populateTabledefArrays();

				if(in_array((string)$variables["tabledefid"], $this->_availableTabledefUUIDs)){

					//check recordid
					if(isset($variables["recordid"])){

						if($this->_availableTabledefUUIDs === NULL || $this->_availableTabledefNames === NULL)
							$this->populateTabledefArrays();

						if(!$this->checkRecordID((string)$variables["recordid"], $this->_availableTabledefNames[$variables["tabledefid"]]))
								$this->verifyErrors[] = "The `recordid` field does match an uuid  in ".$this->_availableTabledefNames[$variables["tabledefid"]].".";

					}else
						$this->verifyErrors[] = "The `recordid` field must be set.";

				}else
					$this->verifyErrors[] = "The `tabledefid` field does not give an existing/acceptable table definition uuids.";

			}else
				$this->verifyErrors[] = "The `tabledefid` field must be set.";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function prepareVariables($variables){

			$variables = parent::prepareVariables($variables);

			if(!isset($variables['primary']))
				$variables['primary'] = 0;

			if(!isset($variables['defaultshipto']))
				$variables['defaultshipto'] = 0;

			if(!isset($variables["existingaddressid"]))
				$variables["existingaddressid"] = false;

			if(isset($variables["id"]))
				if($variables["id"]){// if update

					$variables["id"] = $variables["addressid"];

				}//end if

			return parent::prepareVariables($variables);

		}//end function


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($variables["existingaddressid"]){

				$this->createAddressToRecord($variables, $variables["existingaddressid"], $createdby);

				$newAtrID = $variables["existingaddressid"];

			} else {

				$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

				//create the addresstorecord
				if(!isset($variables["uuid"]))
					$variables["uuid"] = "";

				$newAtrID = $this->createAddressToRecord($variables, $variables["uuid"], $createdby);

			}//endif - existingaddressid

			return $newAtrID;

		}//end method

	}//end class
}//end if


if(class_exists("searchFunctions")){
	class addresstorecordSearchFunctions extends searchFunctions{

		function delete_record(){


			$whereclause=$this->buildWhereClause();


			//We need to iterate through each record
			// to check for cross-record addresses
			$querystatement = "
				SELECT
					*
				FROM
					addresstorecord
				WHERE ".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$removedCount = 0;
			$deletedCount = 0;
			$beenMarked = false;

			while($therecord = $this->db->fetchArray($queryresult)){

				//we can disreguard primary and default shipt to addresses as they
				// cannot be removed
				if(!$therecord["primary"] && !$therecord["defaultshipto"]){

					//look up address to see if it is associated with other records
					$querystatement = "
						SELECT
							id
						FROM
							addresstorecord
						WHERE
							addressid = '".$therecord["addressid"]."'
							AND tabledefid = '".$therecord["tabledefid"]."'
							AND recordid != '".$therecord["recordid"]."'
					";

					$lookupResult = $this->db->query($querystatement);

					if(!$this->db->numRows($lookupResult)){

						//we can safely delete the address (no other associations)
						$deletestatement = "
							DELETE FROM
								addresses
							WHERE
								`uuid` = '".$therecord["addressid"]."'
						";

						$this->db->query($deletestatement);

						$deletedCount++;
						$removedCount--;

					}//end if - numRows

					//remove the connecting record
					$deletestatement = "
						DELETE FROM
							addresstorecord
						WHERE
							id ='".$therecord["id"]."'
					";

					$this->db->query($deletestatement);

					$removedCount++;

				} else {

					$beenMarked = true;

				}//endif - primary or defaultshipto

			}//endwhile - fetchArray

			//next, craft the response

			$message = "";

			if($removedCount){

				$message .= $removedCount." address";

				if($removedCount > 1)
					$message .= "es";

				$message .= " dissociated from record. ";

			}//endif removedCount

			if($deletedCount){

				$message .= $deletedCount." address";

				if($deletedCount > 1)
					$message .= "es";

				$message .= " deleted. ";

			}//endif removedCount

			if($message == "")
				$message = "No addresses removed from record. ";

			if($beenMarked)
				$message .= "(Addresses marked primary or default ship to cannot be removed.)";
			return $message;

		}//end method - delete


		function markPrimary(){

			return $this->_markAs("primary")." primary address.";

		}//end method - markPrimary


		function markDefaultShipTo(){

			return $this->_markAs("defaultshipto")." default ship to address.";

		}//end method - markDefaultShipTo


		function _markAs($what){

			//grab the tabledef and record id from the first selected id
			$querystatement = "
				SELECT
					tabledefid,
					recordid
				FROM
					addresstorecord
				WHERE
					`id` =".((int) $this->idsArray[0]);

			$relatedInfo = $this->db->fetchArray($this->db->query($querystatement));

			//Next, mark all addresses associated with record as false
			$updatestatement = "
				UPDATE
					addresstorecord
				SET
					`".$what."` = 0
				WHERE
					tabledefid = '".$relatedInfo["tabledefid"]."'
					AND recordid = '".$relatedInfo["recordid"]."'
			";

			$this->db->query($updatestatement);

			//Finally, mark the first record.
			$updatestatement = "
				UPDATE
					addresstorecord
				SET
					`".$what."` = 1
				WHERE
					id = ".((int) $this->idsArray[0]);

			$this->db->query($updatestatement);

			// If more than one record was selected, make sure
			// to indicate that only the first record was marked
			$message = "";
			if(count($this->idsArray) > 1)
				$message .= "First ";
			$message .= "Record marked as ";

			return $message;

		}//end method _markAs

	}//end class
}//end if
?>
