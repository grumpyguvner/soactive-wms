<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$thetable = new phpbmstable($db,"tbld:62c4b57e-5adc-951e-a0f8-e1ee05b99e43");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Location";

	$phpbms->cssIncludes[] = "pages/activewms/locations.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/locations.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputField("priority",$therecord["priority"],NULL,false,"integer",8,8);
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("bleepid",$therecord["bleepid"],"bleep table",true,NULL,32,64);
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?><div class="bodyline">
	<?php $theform->startForm($pageTitle);?>

	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p><br/><?php $theform->showField("inactive")?></p>

		<p><?php $theform->showField("priority")?></p>
		<p class="notes">
			Lower priority numbered items are displayed first.
		</p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name</legend>
			<p class="big"><?php $theform->showField("name");?></p>
		</fieldset>
		<fieldset>
			<legend>legacy</legend>
			<p><br />
			<?php $theform->showField("bleepid")?>
			</p>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
