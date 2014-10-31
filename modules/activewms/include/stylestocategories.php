<?php

if(class_exists("searchFunctions")){
	class stylestocategoriesSearchFunctions extends searchFunctions{

                /**
		* deletes style/ marks as inactive
		*/
		function delete_record($useUUID = false){

			$message ="This function is not possible, please use the 'remove from category' function.";

			return $message;

		}

                /**
		* deletes style/ marks as inactive
		*/
		function removeStyles($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause($this->maintable.".id");
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement = "
				UPDATE `stylestocategories` JOIN `styles` ON (`stylestocategories`.`styleid` = `styles`.`uuid`)
                                SET `styles`.`modifiedby` = ".$_SESSION["userinfo"]["id"].",
                                    `styles`.`modifieddate` = NOW()
				WHERE (".$whereclause.")
			";
			$queryresult = $this->db->query($querystatement);

                        $querystatement = "DELETE FROM `".$this->maintable."` WHERE ".$whereclause;
			$queryresult = $this->db->query($querystatement);

//			$message = $this->buildStatusMessage();
//			$message.=" set to inactive.";
			$message ="Styles(s) removed from category.";

			return $message;

		}
	}//end class
}//end if
?>
