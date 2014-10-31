<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

    if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$thetable = new phpbmstable($db,"tbld:85867a3d-df59-ed27-4830-370fd5a1493b");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Product ".$therecord["bleepid"];

	$phpbms->cssIncludes[] = "pages/activewms/products.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/product.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->enctype = "multipart/form-data";

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$temparray = array("In Stock (Available)"=>"In Stock","Out of Stock (Unavailable)"=>"Out of Stock","Back Ordered"=>"Backordered");
		$theinput = new inputBasicList("status",$therecord["status"],$temparray,"availablity");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "styleid",$therecord["styleid"],"styles","uuid","CONCAT(stylenumber,' ',stylename)",
								"inactive=0", "stylenumber", true, "style", true, "");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "sizeid",$therecord["sizeid"],"sizes","uuid","CONCAT(bleepid,' ',name)",
								"inactive=0", "bleepid", true, "size", true, "");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "colourid",$therecord["colourid"],"colours","uuid","CONCAT(bleepid,' ',name)",
								"inactive=0", "bleepid", true, "colour", true, "");
		$theform->addField($theinput);

		$theinput = new inputField("bleepid",$therecord["bleepid"],"plu");
		$theform->addField($theinput);

		$theinput = new inputField("upc",$therecord["upc"],"ean/upc");
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitcost", $therecord["unitcost"], "unit cost");
		$theform->addField($theinput);

		$theinput = new inputField("season",$therecord["season"],"season");
		$theform->addField($theinput);

		$theinput = new inputField("supplierref",$therecord["supplierref"],"supplier ref");
		$theform->addField($theinput);

		$theinput = new inputField("bleep_webstore",$therecord["bleep_webstore"],"webstore");
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputField("bleep_brighton",$therecord["bleep_brighton"],"brighton");
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputField("bleep_cgarden",$therecord["bleep_cgarden"],"exhibitions");
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputField("bleep_whse",$therecord["bleep_whse"],"phantom");
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<!-- <?php $phpbms->showTabs("styles entry","tab:0f687582-f19c-c1d1-eb63-d1bc7359845f",$therecord["id"]);?> -->
    <div class="bodyline">
<!--	<?php $theform->startForm($pageTitle);?> -->
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">

		<fieldset>
			<legend>attributes</legend>


			<p><?php $theform->showField("inactive")?></p>

			<p><?php $theform->showField("bleep_webstore")?></p>
			<p><?php $theform->showField("bleep_brighton")?></p>
			<p><?php $theform->showField("bleep_cgarden")?></p>
			<p><?php $theform->showField("bleep_whse")?></p>

			<p><?php $theform->showField("status")?></p>

                        <p><?php $theform->showField("season")?></p>

		</fieldset>

	</div>

	<div id="leftsideDiv">
		<fieldset>
			<legend>identification</legend>

			<p class="big"><?php $theform->showField("bleepid")?></p>

			<p><?php $theform->showField("styleid") ?></p>
			<p><?php $theform->showField("colourid") ?></p>
			<p><?php $theform->showField("sizeid") ?></p>

		</fieldset>

		<fieldset>
			<legend>supplier</legend>

			<p class="big"><?php $theform->showField("supplierref")?></p>

			<p><?php $theform->showField("upc")?></p>

			<p><?php $theform->showField("unitcost")?></p>

		</fieldset>


                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>


	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		// $theform->endForm();
	?>
</div>
</form>
<?php include("footer.php");?>
