<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");

	$thetable = new phpbmstable($db,"tbld:72be3fb4-7c61-f650-4bfd-b6abff45b383");
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Order";

	$phpbms->cssIncludes[] = "pages/activewms/order.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/order.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("orderdate", $therecord["orderdate"], "order date");
		$theform->addField($theinput);

		$theinput = new inputField("leadsource",$therecord["leadsource"],"sales channel");
		$theform->addField($theinput);

		$theinput = new inputField("webconfirmationno",$therecord["webconfirmationno"],"order ref");
		$theform->addField($theinput);

		$theinput = new inputField("clientid",$therecord["clientid"],"client id");
		$theform->addField($theinput);

		$theinput = new inputField("billingaddressid",$therecord["billingaddressid"],"billing address id");
		$theform->addField($theinput);

		$theinput = new inputField("billtoemail",$therecord["billtoemail"],"bill to email");
		$theform->addField($theinput);

		$theinput = new inputField("billtoname",$therecord["billtoname"],"bill to name");
		$theform->addField($theinput);

		$theinput = new inputField("billtoaddress1",$therecord["billtoaddress1"],"bill to address 1");
		$theform->addField($theinput);

		$theinput = new inputField("billtoaddress2",$therecord["billtoaddress2"],"bill to address 2");
		$theform->addField($theinput);

		$theinput = new inputField("billtocity",$therecord["billtocity"],"bill to city");
		$theform->addField($theinput);

		$theinput = new inputField("billtostate",$therecord["billtostate"],"bill to state");
		$theform->addField($theinput);

		$theinput = new inputField("billtopostcode",$therecord["billtopostcode"],"bill to postcode");
		$theform->addField($theinput);

		$theinput = new inputField("billtocountry",$therecord["billtocountry"],"bill to country");
		$theform->addField($theinput);

		$theinput = new inputField("billtotelephone",$therecord["billtotelephone"],"bill to telephone");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("shiptosameasbilling",$therecord["shiptosameasbilling"],"same as billing");
		$theform->addField($theinput);

		$theinput = new inputField("shiptoaddressid",$therecord["shiptoaddressid"],"shipping address id");
		$theform->addField($theinput);

		$theinput = new inputField("shiptoname",$therecord["shiptoname"],"ship to name");
		$theform->addField($theinput);

		$theinput = new inputField("shiptoaddress1",$therecord["shiptoaddress1"],"ship to address 1");
		$theform->addField($theinput);

		$theinput = new inputField("shiptoaddress2",$therecord["shiptoaddress2"],"ship to address 2");
		$theform->addField($theinput);

		$theinput = new inputField("shiptocity",$therecord["shiptocity"],"ship to city");
		$theform->addField($theinput);

		$theinput = new inputField("shiptostate",$therecord["shiptostate"],"ship to state");
		$theform->addField($theinput);

		$theinput = new inputField("shiptopostcode",$therecord["shiptopostcode"],"ship to postcode");
		$theform->addField($theinput);

		$theinput = new inputField("shiptocountry",$therecord["shiptocountry"],"ship to country");
		$theform->addField($theinput);

		$theinput = new inputField("shiptotelephone",$therecord["shiptotelephone"],"ship to telephone");
		$theform->addField($theinput);

		$theinput = new inputField("statusid",$therecord["statusid"],"status id");
		$theform->addField($theinput);

		$theinput = new inputField("statusdate",$therecord["statusdate"],"status date");
		$theform->addField($theinput);

		$theinput = new inputField("shippingmethod",$therecord["shippingmethod"],"shipping method");
		$theform->addField($theinput);

		$theinput = new inputField("totalweight",$therecord["totalweight"],"total weight");
		$theform->addField($theinput);

		$theinput = new inputField("trackingno",$therecord["trackingno"],"tracking no");
		$theform->addField($theinput);

		$theinput = new inputField("shipping",$therecord["shipping"],"shipping cost");
		$theform->addField($theinput);

		$theinput = new inputField("totalcost",$therecord["totalcost"],"total cost");
		$theform->addField($theinput);

		$theinput = new inputField("promocode",$therecord["promocode"],"promo code");
		$theform->addField($theinput);

		$theinput = new inputField("totaldiscount",$therecord["totaldiscount"],"total discount");
		$theform->addField($theinput);

		$theinput = new inputField("totalti",$therecord["totalti"],"order total");
		$theform->addField($theinput);

		$theinput = new inputTextArea("printedinstructions",$therecord["printedinstructions"],"printed instructions");
		$theform->addField($theinput);

		$theinput = new inputTextArea("specialinstructions",$therecord["specialinstructions"],"special instructions");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("order entry","tab:2e31ab5e-5244-11e0-a791-001e4fae8b91",$therecord["id"]);?>

<div class="bodyline">
	<?php $theform->startForm($pageTitle);?>

	<div id="rightsideDiv">

                <fieldset id="fsAttributes">
                        <legend>attributes</legend>
                        <p><br/><?php $theform->showField("orderdate")?></p>
                        <p><?php $theform->showField("leadsource")?></p>
                        <p><?php $theform->showField("webconfirmationno")?></p>
                        <p><?php $theform->showField("statusid")?></p>
                        <p><?php $theform->showField("statusdate")?></p>
                </fieldset>

                <fieldset>
                        <legend>totals</legend>
                        <p><?php $theform->showField("totalcost")?></p>
                        <p><?php $theform->showField("shipping")?></p>
                        <p><?php $theform->showField("promocode")?></p>
                        <p><?php $theform->showField("totaldiscount")?></p>
                        <p><?php $theform->showField("totalti")?></p>
                </fieldset>

        </div>

	<div id="middleDiv">
		<fieldset >
			<legend>ship to</legend>
			<p class="big"><?php $theform->showField("shiptosameasbilling");?></p>
			<p><?php $theform->showField("shiptoaddressid");?></p>
			<p><?php $theform->showField("shiptoname");?></p>
			<p><?php $theform->showField("shiptoaddress1");?></p>
			<p><?php $theform->showField("shiptoaddress2");?></p>
			<p><?php $theform->showField("shiptocity");?></p>
			<p><?php $theform->showField("shiptostate");?></p>
			<p><?php $theform->showField("shiptopostcode");?></p>
			<p><?php $theform->showField("shiptocountry");?></p>
			<p><?php $theform->showField("shiptotelephone");?></p>
		</fieldset>
		<fieldset >
			<legend>shipping</legend>
			<p><?php $theform->showField("shippingmethod");?></p>
			<p><?php $theform->showField("totalweight");?></p>
			<p><?php $theform->showField("trackingno");?></p>
		</fieldset>

	</div>

	<div id="leftsideDiv">
		<fieldset >
			<legend>bill to</legend>
			<p class="big"><?php $theform->showField("clientid");?></p>
			<p><?php $theform->showField("billingaddressid");?></p>
			<p><?php $theform->showField("billtoemail");?></p>
			<p><?php $theform->showField("billtoname");?></p>
			<p><?php $theform->showField("billtoaddress1");?></p>
			<p><?php $theform->showField("billtoaddress2");?></p>
			<p><?php $theform->showField("billtocity");?></p>
			<p><?php $theform->showField("billtostate");?></p>
			<p><?php $theform->showField("billtopostcode");?></p>
			<p><?php $theform->showField("billtocountry");?></p>
			<p><?php $theform->showField("billtotelephone");?></p>
		</fieldset>

	</div>

	<div>
		<fieldset >
			<legend>instructions</legend>
			<p><?php $theform->showField("printedinstructions");?></p>
			<p><?php $theform->showField("specialinstructions");?></p>
		</fieldset>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
</form>
<?php include("footer.php");?>
