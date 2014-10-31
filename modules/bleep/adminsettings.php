<?php 

	//if we had specific update code for the module, we would create a class
	//called bleepUpdate with a method called updateSettings($variables)
	
	class bleepUpdate{
		/**
		  *  $updateErrorMessage
		  *  @var string An error message that will be displayed.
		  */
		var $updateErrorMessage = "";

		function bleepUpdate($db){

			$this->db = $db;

		}//end method

		function updateSettings($variables){

			if(!isset($variables["bleep_hostname"]))
				$variables["bleep_hostname"] = "localhost";

			if(!isset($variables["bleep_database"]))
				$variables["bleep_database"] = "bleep_imports";

			if(!isset($variables["bleep_user"]))
				$variables["bleep_user"] = "root";

			if(!isset($variables["bleep_password"]))
				$variables["bleep_password"] = "";

			if(!isset($variables["bleep_importuser"]))
				$variables["bleep_importuser"] = "2";

			return $variables;

		}//end method

	}
	
	
	// if you want to display fields on the configuration screen
	// follow the class template below
	
	class bleepDisplay{
		
		function getFields($therecord){
			// here you define any special fields you may need
			//$therecord is the array of settings

			$fields = array();
			
			//bleep database hostname/ip
			$theinput = new inputField("bleep_host",$therecord["bleep_host"],"bleep host",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep database name
			$theinput = new inputField("bleep_database",$therecord["bleep_database"],"bleep database",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep database username
			$theinput = new inputField("bleep_user",$therecord["bleep_user"],"bleep user",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep database password
			$theinput = new inputField("bleep_password",$therecord["bleep_password"],"bleep password",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep import userid
			$theinput = new inputField("bleep_importuser",$therecord["bleep_importuser"],"user id for imports",true,"integer",4,4);
			$fields[] = $theinput;		
		
			return $fields;
		}//end method
		
		function display($theform,$therecord){
?>
<div class="moduleTab" title="database">
<fieldset>
	<legend>database</legend>

	<p><?php echo $theform->showField("bleep_host");?></p>

	<p><?php echo $theform->showField("bleep_database");?></p>

	<p><?php echo $theform->showField("bleep_user");?></p>

	<p><?php echo $theform->showField("bleep_password");?></p>

	<p><?php echo $theform->showField("bleep_importuser");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<?php
		}//end method
	}//end class
?>