<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$thetable = new phpbmstable($db,"tbld:a7747b7b-aba6-53c0-f1a7-cb34fb800e82");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Supplier";

	$phpbms->cssIncludes[] = "pages/activewms/suppliers.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/suppliers.js";

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

		$theinput = new inputField("contact",$therecord["contact"],"name",false,NULL,32,128);
		$theform->addField($theinput);

		$theinput = new inputField("contact_email",$therecord["contact_email"],"email",false,"email",32,128);
		$theform->addField($theinput);

		$theinput = new inputField("contact_telephone",$therecord["contact_telephone"],"telephone","phone",NULL,32,128);
		$theform->addField($theinput);

                $theinput = new inputTextarea("memo", $therecord["memo"], "memo", false, 8, 96, false);
                $theform->addField($theinput);

		$theinput = new inputField("bleepid",$therecord["bleepid"],"bleep id",false,"integer",8,8);
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
			<legend>contact</legend>
			<p><br />
			<?php $theform->showField("contact")?><br />
			<?php $theform->showField("contact_email")?><br />
			<?php $theform->showField("contact_telephone")?>
			</p>
		</fieldset>
		<fieldset>
			<legend>legacy</legend>
			<p><br />
			<?php $theform->showField("bleepid")?>
			</p>
		</fieldset>

		<fieldset>
			<legend><label for="memo">memo</label></legend>

	                <p><?php $theform->showField("memo");?></p>

		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
