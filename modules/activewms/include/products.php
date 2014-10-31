<?php

include_once("include/tables.php");
include_once("include/fields.php");

if(class_exists("phpbmsTable")){

	class products extends phpbmsTable{

                var $availablePLUs = NULL;

		function verifyVariables($variables){

			//must have a PLU ... table default is not enough
			if(isset($variables["bleepid"])){

				//must have some sort of PLU
				if($variables["bleepid"] !== "" || $variables["bleepid"] !== NULL){

					if($this->availablePLUs === NULL)
						$this->populatePLUArray();

					//can't have this PLU already chosen
					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					$tempPLU = $variables["bleepid"];// using this because it looks ugly to put the brackets within brackets
					if( array_key_exists($variables["bleepid"], $this->availablePLUs) ){
                                                if( $this->availablePLUs[$tempPLU]["id"] !== $tempid )
                                                    $this->verifyErrors[] = "The `PLU` field must be a unique number, ".$variables["bleepid"]." is already being used.";
					}else{
						$this->availablePLUs[$tempPLU]["id"] = "aoihweoighaow giuahrweughauerhgaiudsf iaheiugaiuweg iagweiuha wiueg";// impossible id put in
					}//end if

				}else
					$this->verifyErrors[] = "The `PLU` field must not be blank.";

			}else
                                $this->verifyErrors[] = "The `PLU` field must be set.";


			//must have a styleid
                        if(isset($variables["styleid"])){
                                //must have some sort of styleid
				if($variables["styleid"] !== "" || $variables["styleid"] !== NULL){
                                        //Any name validation needs to go here
				}else
					$this->verifyErrors[] = "The `style` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `style` field must be set.";

			//must have a colourid
                        if(isset($variables["colourid"])){
                                //must have some sort of colourid
				if($variables["colourid"] !== "" || $variables["colourid"] !== NULL){
                                        //Any name validation needs to go here
				}else
					$this->verifyErrors[] = "The `colour` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `colour` field must be set.";

                        //must have a sizeid
                        if(isset($variables["sizeid"])){
                                //must have some sort of sizeid
				if($variables["sizeid"] !== "" || $variables["sizeid"] !== NULL){
                                        //Any name validation needs to go here
				}else
					$this->verifyErrors[] = "The `size` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `size` field must be set.";


			return parent::verifyVariables($variables);

		}//end method --verifyVariables--

		function populatePLUArray(){

			// I need id as well to let updates work with our verify function
			// i.e. if its an update on existing record, its ok if the stylenumber
			// is not unique iff its already associated to the record being updated
			$this->availablePLUs = array();

			$querystatement = "
				SELECT
					`id`,
					`bleepid`
				FROM
					`products`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){

				while($therecord = $this->db->fetchArray($queryresult)){

					$bleepid = $therecord["bleepid"];
					$id = $therecord["id"];

					$this->availablePLUs[$bleepid]["id"] = $id;

				}//end while

			}//endif

		}//end method --populatePLUArray--

	}//end class styles

}//end if

if(class_exists("searchFunctions")){
	class stylesSearchFunctions extends searchFunctions{
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

if(class_exists("searchFunctions")){
	class productsSearchFunctions extends searchFunctions{
		/**
		* deletes product/ marks as inactive
		*/
		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause("styles.id");
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement = "
				UPDATE `products`
                                SET `inactive` = 1
				WHERE (".$whereclause.")
			";

			$queryresult = $this->db->query($querystatement);

			$message ="Record(s) set as inactive.";

			return $message;

		}
	}//end class
}//end if

if(class_exists("phpbmsImport")){
	class productsImport extends phpbmsImport{

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
                                            case "OUR STYLE CODE":
                                            case "OUR STYLE":
                                                $columns["styleid"]=$value;
                                                break;
                                            case "BRAND":
                                            case "SUPPLIER":
                                                $columns["supplierid"]=$value;
                                                break;
                                            case "COLOUR CODE":
                                            case "COLOUR":
                                                $columns["colourid"]=$value;
                                                break;
                                            case "SIZE CODE":
                                            case "SIZE":
                                                $columns["sizeid"]=$value;
                                                break;
                                            case "UNIT COST":
                                            case "COST @ DISCOUNT":
                                                $columns["unitcost"]=$value;
                                                break;
                                            case "SUPPLIER CODE":
                                            case "SUPPLIER REF":
                                                $columns["supplierref"]=$value;
                                                break;
                                        }
                                    }

                                    //Verify required column names are present
                                    $checkRecords = true;
                                    if(!isset($columns["styleid"])){
                                        $this->error .= '<li> Required column missing [Style Code] or [Our Style]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["supplierid"])){
                                        $this->error .= '<li> Required column missing [Brand] or [Supplier]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["colourid"])){
                                        $this->error .= '<li> Required column missing [Colour]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["sizeid"])){
                                        $this->error .= '<li> Required column missing [Size]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["unitcost"])){
                                        $this->error .= '<li> Required column missing [Unit Cost]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!isset($columns["supplierref"])){
                                        $this->error .= '<li> Required column missing [Supplier Code]</li>';
                                        $checkRecords = false;
                                    }
                                    if(!$checkRecords)
                                        //Do not bother to check the records of the titles are wrong.
                                        break;

                                    //the file starts at line number 1, but since line 1 is
                                    //supposed to be the fieldnames in the table(s), the lines
                                    //being insereted start @ 2.
                                    $rowNum = 2;
                                    $querystatement = "SELECT `value` FROM `settings` WHERE `name` = 'ACTIVEWMS_NEXTPLU' LIMIT 1;";
                                    $queryresult = $this->table->db->query($querystatement);
                                    $therecord = $this->table->db->fetchArray($queryresult);
                                    if(!isset($therecord["value"])){
                                        $newPLU = 10000001000;
                                        $querystatement = "INSERT INTO `settings` (`id`,`name`,`value`) VALUES (NULL,'ACTIVEWMS_NEXTPLU','".$newPLU."');";
                                        $queryresult = $this->table->db->query($querystatement);
                                    }else{
                                        $newPLU = $therecord["value"];
                                    }

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
                                                    case "styleid":
//                                                        $querystatement = "SELECT `uuid` FROM `styles` WHERE `stylename` = CONCAT('".trim($rowData[$key])."',' - ','".trim($rowData['colourid'])."') LIMIT 1;";
                                                        $querystatement = "SELECT `uuid` FROM `styles` WHERE `stylenumber` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]) && trim($rowData[$key]!=""))
//                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).' - '.trim($rowData['colourid']).'] for line number '.$rowNum.'.</li>';
                                                            $this->error .= '<li> unknown Style ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        break;
                                                    case "supplierid":
                                                        $querystatement = "SELECT `uuid` FROM `suppliers` WHERE `name` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]))
                                                            $this->error .= '<li> unknown Brand ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        break;
                                                    case "colourid":
                                                        $querystatement = "SELECT `uuid` FROM `colours` WHERE `name` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]))
                                                            $this->error .= '<li> unknown Colour ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        break;
                                                    case "sizeid":
                                                        $querystatement = "SELECT `uuid` FROM `sizes` WHERE `name` = '".trim($rowData[$key])."' LIMIT 1;";
                                                        $queryresult = $this->table->db->query($querystatement);
                                                        $therecord = $this->table->db->fetchArray($queryresult);
                                                        if(!isset($therecord["uuid"]))
                                                            $this->error .= '<li> unknown Size ['.trim($rowData[$key]).'] for line number '.$rowNum.'.</li>';
                                                        $trimmedRowData[$name] = $therecord["uuid"];
                                                        break;
                                                    default:
                                                        $trimmedRowData[$name] = trim($rowData[$key]);
                                                }
                                            }

                                            if(!isset($trimmedRowData['bleepid'])){

                                                // Automatically set next available PLU number:
                                                $exists=true;
                                                while ($exists) {
                                                    $newPLU++;
                                                    $querystatement = "SELECT `uuid` FROM `products` WHERE `bleepid` = '".str_pad($newPLU, 11, "0", STR_PAD_LEFT)."';";
                                                    $queryresult = $this->table->db->query($querystatement);
                                                    $exists = ($this->table->db->numRows($queryresult)>0);
//                                                    $exists = false;
                                                }
                                                $trimmedRowData["bleepid"]=str_pad($newPLU, 11, "0", STR_PAD_LEFT);
                                                $querystatement = "UPDATE IGNORE `settings` SET `value` = '".$trimmedRowData["bleepid"]."' WHERE `name`='ACTIVEWMS_NEXTPLU';";
                                                $queryresult = $this->table->db->query($querystatement);
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
}//end if
?>
