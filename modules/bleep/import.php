<?php

	include_once("include/db.php");
	$db = new db();

function processSizes() {
  $con = mysql_connect(BLEEP_HOSTNAME,BLEEP_USER,BLEEP_PASSWORD);
  if (!$con)  {
    die('Could not connect: ' . mysql_error()); }

  mysql_select_db(BLEEP_DATABASE, $con);

  $sql="SELECT * FROM SIZE";

  if (!mysql_query($sql,$con)) {
    die('Error: ' . mysql_error()); }

  echo "Found ? records";

  mysql_close($con);
}

function sendemail() {
  $to = "mark@hortonconsulting.co.uk";
  $subject = "Test mail";
  $message = "$_POST[message]";
  $from = "$_POST[email]";
  $headers = "From: $from";
  mail($to,$subject,$message,$headers);
}

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	include("include/importlogs.php");

	$thetable = new importlogs($db,"tbld:d6e4e1fb-4bfa-cb53-ab9c-1b3e7f907ae2");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Bleep Imports";
	$phpbms->cssIncludes[] = "pages/importlogs.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("priority",$therecord["priority"],NULL,false,"integer",8,8);
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "defaultassignedtoid", "Pick Active User", $therecord["defaultassignedtoid"], "assigned to", false, 42);
		$theform->addField($theinput);

		//==============================================================
		//End Form Elements

	include("header.php");

?>
<div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>attributes</legend>

		<p><br /><?php $theform->showField("inactive")?></p>

		<p><?php $theform->showField("priority")?></p>

		<p class="notes">Lower priority numbered items are displayed first.</p>

	</fieldset>

	<div id="nameDiv">
		<fieldset>
			<legend>Defaults</legend>
			<p>
				<?php $theform->showField("defaultassignedtoid")?>
			</p>
		</fieldset>

        </div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
