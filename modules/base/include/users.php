<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/
if(class_exists("phpbmsTable")){
	class users extends phpbmsTable{

		var $usedLoginNames = NULL;

		function populateLoginNameArray(){

			$querystatement="
				SELECT
					`id`,
					`login`
				FROM
					`users`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				while($therecord = $this->db->fetchArray($queryresult)){

					$login = $therecord["login"];
					$id = $therecord["id"];
					$this->usedLoginNames[$login]["id"] = $id;

				}//end while
			}//end if

		}//end method


		function verifyVariables($variables){
			//---------[ check login names ]------------------------------

			if(isset($variables["login"])){
				if( $variables["login"] !== "" || $variables["login"] !== NULL ){

					if($this->usedLoginNames === NULL)
						$this->populateLoginNameArray();

					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					if($tempid < 0)
						$tempid = 0;

					//check to see new login name is taken
					$templogin = $variables["login"];// using this because it looks ugly to but the brackets within brackets
					if( array_key_exists($variables["login"], $this->usedLoginNames) ){

						if( $this->usedLoginNames[$templogin]["id"] !== $tempid )
							$this->verifyErrors[] = "The `login` field must give an unique login name.";

					}else{
						$this->availableProducts[$templogin]["id"] = -1;// impossible id put in (besides the type will throw off the if anyways)
					}//end if

				}else
					$this->verifyErrors[] = "The `login` field must not be blank.";
			}else
				$this->verifyErrors[] = "The `login` field must be set.";

			//---------[ check email ]---------------------------------
			//if(isset($variables["email"]))
			//	if( $variables["email"] !== NULL && $variables["email"] !== "" && !validateEmail($variables["email"]))
			//		$this->verifyErrors[] = "The `email` field must have a valid email or must be left blank.";

			//---------[ check booleans ]---------------------------------
			if(isset($variables["revoked"]))
				if($variables["revoked"] && $variables["revoked"] != 1)
					$this->verifyErrors[] = "The `revoked` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["portalaccess"])){
				if($variables["portalaccess"] && $variables["portalaccess"] != 1)
					$this->verifyErrors[] = "The `portalaccess` field must be a boolean (equivalent to 0 or exactly 1).";

				if($variables["portalaccess"]){

					if(isset($variables["admin"]))
						if(!$variables["admin"])
							$this->verifyErrors[] = "The `admin` field must be '1' if `portalaccess` is '1'.";

				}//end if

			}//end if

			if(isset($variables["admin"]))
				if($variables["admin"] && $variables["admin"] != 1)
					$this->verifyErrors[] = "The `admin` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			if($variables["password"])
				$this->fields["password"]["type"] = "password";
			else
				unset($this->fields["password"]);

			unset($this->fields["lastlogin"]);

			parent::updateRecord($variables, $modifiedby, $useUuid);

			if($variables["roleschanged"]==1)
				$this->assignRoles($variables["uuid"],$variables["newroles"]);

			//reset field information
			$this->fields = $this->db->tableInfo($this->maintable);
		}


		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			$this->fields["password"]["type"] = "password";
			unset($this->fields["lastlogin"]);

			$theid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			//reset field information
			$this->fields = $this->db->tableInfo($this->maintable);

			return $theid;
		}

		/**
		 * assigns roles to a user
		 * @param string $id uuid of user
		 * @param string $roles comma separated list of roles to insert.
		 */
		function assignRoles($id,$roles){

		    $deletestatement = "
				DELETE FROM
					rolestousers
				WHERE
					userid = '".$id."'";

			$queryresult = $this->db->query($deletestatement);

			$newroles = explode(",", $roles);

			foreach($newroles as $therole)
				if($therole != ""){

					$insertstatement = "
						INSERT INTO
							rolestousers
							(userid,roleid)
						VALUES
							('".$id."', '".$therole."')";

					$this->db->query($insertstatement );

				}//endif

		}//end function assignRoles


		/**
		 * displays the add roles select boxes
		 *
		 * @param string $id user's uuid
		 * @param strng $type available/selected
		 */
		function displayRoles($id, $type){

			$querystatement="
				SELECT
					roles.uuid,
					roles.name
				FROM
					roles INNER JOIN rolestousers ON rolestousers.roleid = roles.uuid
				WHERE
					rolestousers.userid= '".mysql_real_escape_string($id)."'";

			$assignedquery = $this->db->query($querystatement);

			$thelist = array();

			if($type == "available"){

					$excludelist = array();

					while($therecord = $this->db->fetchArray($assignedquery))
						$excludelist[] = $therecord["uuid"];

					$querystatement = "
						SELECT
							uuid,
							name
						FROM
							roles
						WHERE
							inactive = 0";

					$availablequery = $this->db->query($querystatement);

					while($therecord = $this->db->fetchArray($availablequery))
						if(!in_array($therecord["uuid"], $excludelist))
							$thelist[] = $therecord;

			} else
				while($therecord = $this->db->fetchArray($assignedquery))
					$thelist[] = $therecord;

			foreach($thelist as $theoption){
				?>
				<option value="<?php echo $theoption["uuid"]?>"><?php echo htmlQuotes($theoption["name"])?></option>
				<?php
			}//endif

		}//end function displayRoles

	}//end class

}//end if

if(class_exists("searchFunctions")){
	class usersSearchFunctions extends searchFunctions{

		function delete_record($useUUID = false){

			if(!$useUUID)
				$whereclause=$this->buildWhereClause();
			else
				$whereclause = $this->buildWhereClause($this->maintable.".uuid");

			$querystatement = "
				UPDATE
					`users`
				SET
					`revoked` = '1',
					`modifiedby` = '".$_SESSION["userinfo"]["id"]."'
				WHERE
					".$whereclause;

			$queryresult = $this->db->query($querystatement);

			$message = $this->buildStatusMessage();
			$message.=" revoked access.";
			return $message;
		}


	}//end class
}//end if

?>
