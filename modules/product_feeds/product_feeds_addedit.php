<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$thetable = new phpbmstable($db,"tbld:7f9b7006-888f-11e1-8108-001d0923519e");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Product Feeds";

	$phpbms->cssIncludes[] = "pages/product_feeds/product_feeds.css";
//	$phpbms->jsIncludes[] = "modules/product_feeds/javascript/colours.js";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();

        $theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
        $theinput->setAttribute("class","important");
        $theform->addField($theinput);

        $theinput = new inputChoiceList($db,"site",$therecord["site"],"sites", "site");
        $theform->addField($theinput);

        $theinput = new inputDataTableList($db, "tabledefid",$therecord["tabledefid"],"tabledefs","uuid","displayname",
                                                        "moduleid='mod:fe626fcc-888d-11e1-917a-001d0923519e'
                                                            AND uuid<>'tbld:7f9b7006-888f-11e1-8108-001d0923519e'",
                                                        "displayname", false, "feed definition", true, "(please choose)");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputField("filename",$therecord["filename"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("fileformat",$therecord["fileformat"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("uploadurl",$therecord["uploadurl"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("username",$therecord["username"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("password",$therecord["password"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("priority",$therecord["priority"],NULL,false,"integer",8,8);
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
			Lower priority numbered items are processed first.
		</p>
	          <p><br/><?php $theform->showField("site")?></p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name</legend>
			<p class="big"><?php $theform->showField("name");?></p>

			<p><?php $theform->showField("tabledefid") ?></p>
		</fieldset>
		<fieldset>
			<legend>file details</legend>
			<p><br />
			<?php $theform->showField("filename")?>
			</p>
			<p><br />
			<?php $theform->showField("fileformat")?>
			</p>
			<p><br />
			<?php $theform->showField("uploadurl")?>
			</p>
			<p><br />
			<?php $theform->showField("username")?>
			</p>
			<p><br />
			<?php $theform->showField("password")?>
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
