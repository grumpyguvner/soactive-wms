<?php

if(class_exists("phpbmsTable")){
	class importlogs extends phpbmsTable {

		var $_availableUserUUIDs = NULL;


		function verifyVariables($variables){

			if(isset($variables["setreadytopost"]))
				if($variables["setreadytopost"] && $variables["setreadytopost"] != 1)
					$this->verifyErrors[] = "The `setreadytopost` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["invoicedefault"]))
				if($variables["invoicedefault"] && $variables["invoicedefault"] != 1)
					$this->verifyErrors[] = "The `invoicedefault` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["defaultassignedtoid"])){

				if($this->_availableUserUUIDs === NULL){
					$this->_availableUserUUIDs = $this->_loadUUIDList("users");
					$this->_availableUserUUIDs[] = "";//for everyone/no one
				}//end if

				if(!in_array(((string)$variables["defaultassignedtoid"]), $this->_availableUserUUIDs))
					$this->verifyErrors[] = "The `defaultassignedtoid` field does not give an existing/acceptable user uuid.";

			}//end if

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){
			if(isset($variables["invoicedefault"]))
				$this->updateInvoiceDefault();

			parent::updateRecord($variables, $modifiedby, $useUuid);
		}

		function updateInvoiceDefault(){
			$querystatement="UPDATE `".$this->maintable."` SET `invoicedefault` = 0";
			$queryresult = $this->db->query($querystatement);
		}
	}
}

if(class_exists("searchFunctions")){
	class importlogsSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			//$whereclause = $this->buildWhereClause($theids,"importlogs.id");

			$querystatement = "
				UPDATE
					`importlogs`
				SET
					`inactive`='1',
					`modifiedby`='".$_SESSION["userinfo"]["id"]."'
				WHERE
					(".$whereclause.")
				AND
					`invoicedefault`='0'
			";

			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message .= " marked inactive.";
			return $message;
		}

	}//end class
}

?>