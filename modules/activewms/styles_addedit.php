<?php
	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/styles.php");

    if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$thetable = new styles($db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42", $backurl);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle="Style ".$therecord["stylenumber"];

	$phpbms->cssIncludes[] = "pages/activewms/styles.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/style.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->enctype = "multipart/form-data";

//		$theinput = new inputField("bleepstock",$therecord["bleep_stock"],NULL,false,"real");
//		$theform->addField($theinput);

		$theinput = new inputField("stylenumber",$therecord["stylenumber"],"style number",true);
                //allow entry of style number if creating a new record
                if(!$therecord["stylenumber"]==""){
                    $theinput->setAttribute("readonly", "readonly");
                    $theinput->setAttribute("class", "uneditable");
                }
		$theform->addField($theinput);

		$theinput = new inputField("stylename",$therecord["stylename"],"name");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "supplierid",$therecord["supplierid"],"suppliers","uuid","name",
								"inactive=0", "name", true, "supplier", true, "");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("taxable",$therecord["taxable"]);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("inc_brighton",$therecord["inc_brighton"]);
		$theform->addField($theinput);

		$temparray = array("Inventory"=>"Inventory","Non-Inventory"=>"Non-Inventory","Service"=>"Service","Kit"=>"Kit","Assembly"=>"Assembly");
		$theinput = new inputBasicList("type",$therecord["type"],$temparray);
		$theform->addField($theinput);

		$temparray = array("In Stock (Available)"=>"In Stock","Out of Stock (Unavailable)"=>"Out of Stock","Back Ordered"=>"Backordered");
		$theinput = new inputBasicList("status",$therecord["status"],$temparray,"availablity");
		$theform->addField($theinput);

		$theinput = new inputField("altstyles",$therecord["bleep_alt_styles"],"alt. styles");
		$theform->addField($theinput);

		$theinput = new inputField("alt_images",$therecord["alt_images"],"alt. images");
		$theform->addField($theinput);

		$theinput = new inputField("season",$therecord["season"],"season");
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitprice", $therecord["unitprice"], "unit price" ,true);
                $theinput->setAttribute("class", "important");
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitcost", $therecord["unitcost"], "unit cost (ex. VAT)");
		$theform->addField($theinput);

		$theinput = new inputCurrency("saleprice", $therecord["saleprice"], "sale price");
		$theform->addField($theinput);

		$markup=0;
		if($therecord["unitcost"]!=0)
			$markup=round(1-($therecord["unitcost"]/($therecord["unitprice"]/1.2)),4)*100;
//			$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;

		$theinput = new inputPercentage("markup", $markup, "mark-up",2);
		$theinput->setAttribute("size","10");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("salestartdate", $therecord["salestartdate"], "start date");
		$theform->addField($theinput);

		$theinput = new inputDatePicker("saleenddate", $therecord["saleenddate"], "end date");
		$theform->addField($theinput);

		$theinput = new inputField("unitofmeasure",$therecord["unitofmeasure"],"unit of measure");
		$theform->addField($theinput);

		$theinput = new inputField("weight",$therecord["weight"],NULL,false,"real");
		$theform->addField($theinput);

		if ($therecord["packagesperitem"])
			$itemsperpackage=1/$therecord["packagesperitem"];
		else
			$itemsperpackage=NULL;

		$theinput = new inputField("packagesperitem",$itemsperpackage,NULL,false,"real",10,16,false);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("isprepackaged",$therecord["isprepackaged"],"prepackaged");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("isoversized",$therecord["isoversized"],"oversized");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "producttypeid",$therecord["producttypeid"],"producttypes","uuid","name",
								"inactive=0", "name", true, "product type", true, "");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "sizeguideid",$therecord["sizeguideid"],"sizeguides","uuid","name",
								"inactive=0", "name", true, "size guide", true, "");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "genderid",$therecord["genderid"],"genders","uuid","name",
								"inactive=0", "name", true, "gender", true, "");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "agegroupid",$therecord["agegroupid"],"agegroups","uuid","name",
								"inactive=0", "name", true, "age group", true, "");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "morecategories", "Pick Style Category For Style", "", "category");
		$theform->addField($theinput);

		$theinput = new inputField("description",$therecord["description"],"description",false,NULL,96,255);
		$theform->addField($theinput);

		$theinput = new inputField("keywords",$therecord["keywords"],"keywords",false,NULL,96,255);
		$theform->addField($theinput);

		$theinput = new inputTextarea("webdescription",$therecord["webdescription"],"webdescription", false, 4, 48, false);
		$theform->addField($theinput);

                $theinput = new inputTextarea("memo", $therecord["memo"], "memo", false, 4, 48, false);
                $theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("styles entry","tab:0f687582-f19c-c1d1-eb63-d1bc7359845f",$therecord["id"]);?>
    <div class="bodyline">
<!--	<?php $theform->startForm($pageTitle);?> -->
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">

		<fieldset>
			<legend>image</legend>
        			<img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo $therecord["image_ids"][0] ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
		</fieldset>

		<fieldset>
			<legend>attributes</legend>


			<p><?php $theform->showField("inactive")?></p>

                        <p><?php $theform->showField("webenabled")?></p>

			<p><?php $theform->showField("genderid") ?></p>

			<p><?php $theform->showField("agegroupid") ?></p>

			<p><?php $theform->showField("producttypeid") ?></p>

			<p><?php $theform->showField("sizeguideid") ?></p>

			<p><?php $theform->showField("type")?></p>

<!--			<p><?php $theform->showField("bleepstock")?></p> -->

			<p><?php $theform->showField("status")?></p>

			<p><?php $theform->showField("taxable")?></p>

			<p><?php $theform->showField("inc_brighton")?></p>

			<p><?php $theform->showField("altstyles")?></p>

			<p><?php $theform->showField("alt_images")?></p>

			<p><?php $theform->showField("season")?></p>

		</fieldset>

                <fieldset>
                    <legend>Categories</legend>
                    <p>
                        <?php $theform->showField("morecategories")?>
                        <button type="button" id="addCatButton" class="graphicButtons buttonPlus" title="Add Category"><span>+</span></button>
                    </p>

                    <?php $thetable->displayAdditionalStyleCategories($therecord["addcats"]) ?>

		    <p>
	                <label for="categoryid">default category</label><br />
        	        <?php $thetable->displayStyleCategories($therecord["categoryid"]) ?>
		    </p>

                </fieldset>

	</div>

	<div id="leftsideDiv">
		<fieldset>
			<legend>identification</legend>

			<p class="big" id="styleNameP"><?php $theform->showField("stylename")?></p>

			<p class="big"><?php $theform->showField("stylenumber") ?></p>

			<p><?php $theform->showField("supplierid") ?></p>

		</fieldset>

		<fieldset>
			<legend>price / cost</legend>


			<p class="costsP"><?php $theform->showField("unitprice")?> = </p>

			<p class="costsP"><?php $theform->showField("unitcost")?> + </p>

			<p class="costsP"><?php $theform->showField("markup")?></p>

			<p>
			    <br />
                <button type="button" id="updatePrice" class="Buttons">calculate price</button>
			</p>

		</fieldset>

		<fieldset>
			<legend>sale / promotion</legend>

			<p class="costsP"><?php $theform->showField("saleprice")?></p>

			<p class="costsP"><?php $theform->showField("salestartdate")?></p>

			<p class="costsP"><?php $theform->showField("saleenddate")?></p>

		</fieldset>
		<fieldset>
			<legend>web</legend>

			<p><?php $theform->showField("description") ?></p>
			<p><?php $theform->showfield("keywords");?> <span class="notes">(comma separated key word list)</span></p>

                        <div class="fauxP">
                                <label for="webdescription">web description <span class="notes">(HTML acceptable)</span></label><br />

                                <div style=" <?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
                                        <textarea id="webdescription" name="webdescription" cols="60" rows="6"><?php echo $therecord["webdescription"] ?></textarea>
                                </div>
                                <div style=" <?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
                                <?php echo $therecord["webdescription"] ?>
                                </div>
                                <div><button id="buttonWebPreview" type="button" class="Buttons"><?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?></button></div>

                        </div>

		</fieldset>

                <fieldset>
                    <legend>Weight / Measurements</legend>

                    <p><?php $theform->showField("weight")?></p>

                    <p><?php $theform->showField("unitofmeasure")?></p>

                </fieldset>

		<fieldset>
			<legend>shipping</legend>
			<p>
				<label for="packagesperitem">items per package <span class="notes">(number of style items that can fit in a shipping package)</span></label><br />
				<?php $theform->showfield("packagesperitem")?>
			</p>

			<p><?php $theform->showfield("isprepackaged");?> <span class="notes">(style is not packed with any other style.)</span></p>

			<p><?php $theform->showfield("isoversized");?> <span class="notes">(style must be delivered in a box designated as oversized for shipping purposes.)</span></p>

                </fieldset>

		<fieldset>
			<legend><label for="memo">memo</label></legend>

	                <p><?php $theform->showField("memo");?></p>

		</fieldset>



                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>


	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
	//	$theform->endForm();
	?>
</div>
</form>
<?php include("footer.php");?>
