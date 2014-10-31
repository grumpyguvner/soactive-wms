<?php 

	//if we had specific update code for the module, we would create a class
	//called bleep_emailUpdate with a method called updateSettings($variables)
	
	class bleep_emailUpdate{
		/**
		  *  $updateErrorMessage
		  *  @var string An error message that will be displayed.
		  */
		var $updateErrorMessage = "";

		function bleep_emailUpdate($db){

			$this->db = $db;

		}//end method

		function updateSettings($variables){

			if(!isset($variables["bleep_email_hostname"]))
				$variables["bleep_email_hostname"] = "m.sheactive.net";

			if(!isset($variables["bleep_email_user"]))
				$variables["bleep_email_user"] = "sales.sheactive";

			if(!isset($variables["bleep_email_password"]))
				$variables["bleep_email_password"] = "5h3Act!ve";

			return $variables;

		}//end method

	}
	
	
	// if you want to display fields on the configuration screen
	// follow the class template below
	
	class bleep_emailDisplay{
		
		function getFields($therecord){
			// here you define any special fields you may need
			//$therecord is the array of settings

			$fields = array();
			
			//bleep_email hostname/ip
			$theinput = new inputField("bleep_email_hostname",$therecord["bleep_email_hostname"],"bleep_email hostname",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep_email database username
			$theinput = new inputField("bleep_email_user",$therecord["bleep_email_user"],"bleep_email user",true,NULL,32,128);
			$fields[] = $theinput;		
			//bleep_email database password
			$theinput = new inputField("bleep_email_password",$therecord["bleep_email_password"],"bleep_email password",true,NULL,32,128);
			$fields[] = $theinput;		
		
			return $fields;
		}//end method
		
		function display($theform,$therecord){
?>
<div class="moduleTab" title="account">
<fieldset>
	<legend>Email Account</legend>

	<p><?php echo $theform->showField("bleep_email_hostname");?></p>

	<p><?php echo $theform->showField("bleep_email_user");?></p>

	<p><?php echo $theform->showField("bleep_email_password");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<?php
		}//end method
	}//end class
?>