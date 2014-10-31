<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/brochure_requests.php");

	$thetable = new brochure_requests($db);
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

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputField("address",$therecord["address"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("city",$therecord["city"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("postal_code",$therecord["postal_code"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("country",$therecord["country"],NULL,false,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("carrier_sheet",$therecord["carrier_sheet"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("reference",$therecord["reference"],NULL,true,NULL,32,64);
        $theform->addField($theinput);

        $theinput = new inputField("undelivered",$therecord["undelivered"],NULL,false,"integer",8,8);
        $theform->addField($theinput);

        $theinput = new inputField("duplicate_id",$therecord["duplicate_id"],NULL,false,"integer",8,8);
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

		<p><?php $theform->showField("undelivered")?></p>
		<p class="notes">
			Number of times an undelivered brochure has been returned.
		</p>
	          <p><br/><?php $theform->showField("site")?></p>
	          <p><br/><?php $theform->showField("reference")?></p>
	          <p><br/><?php $theform->showField("duplicate_id")?></p>
	</fieldset>

	<div id="nameDiv">
		<fieldset >
			<legend>name</legend>
			<p class="big"><?php $theform->showField("name");?></p>
		</fieldset>
		<fieldset>
			<legend>details</legend>
			<p><br />
			<?php $theform->showField("address")?>
			</p>
			<p><br />
			<?php $theform->showField("city")?>
			</p>
			<p><br />
			<?php $theform->showField("postal_code")?>
			</p>
			<p><br />
			<?php $theform->showField("country")?>
			</p>
			<p><br />
			<?php $theform->showField("carrier_sheet")?>
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
