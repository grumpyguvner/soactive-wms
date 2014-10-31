<?php

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/styles_translations.php");

    if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$thetable = new styles_translations($db, "tbld:061dd57e-df95-a18b-03e4-fcfe43fad7df", $backurl);
	$therecord = $thetable->processAddEditPage();
	
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];
	
	$pageTitle="Site Specific Data";
	
	$phpbms->cssIncludes[] = "pages/activewms/styles.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/styletranslations.js";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "styleid",$therecord["styleid"],"styles","uuid","stylename",
								"inactive=0", "stylename", true, "style", true, "");
		$theinput->setAttribute("readonly","readonly");
		$theinput->setAttribute("class","uneditable");
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db,"site",$therecord["site"],"sites", "site");
		$theinput->setAttribute("readonly","readonly");
		$theinput->setAttribute("class","uneditable");
		$theform->addField($theinput);
	
		$theinput = new inputField("stylename",$therecord["stylename"],"name",true,NULL,32,128);
		$theform->addField($theinput);
	
		$theinput = new inputField("description", $therecord["description"],false,NULL,96,255);
		$theform->addField($theinput);
	
		$theinput = new inputTextarea("webdescription",$therecord["webdescription"],"webdescription", false, 4, 48, false);
		$theform->addField($theinput);

		$theinput = new inputField("price", $therecord["price"], "price" ,true);
//                $theinput->setAttribute("class", "important");
		$theform->addField($theinput);

		$theinput = new inputField("wholesale_price", $therecord["wholesale_price"], "wholesale (HT)");
		$theform->addField($theinput);

		$markup=0;
		if($therecord["wholesale_price"]!=0)
			$markup=round(($therecord["price"]/$therecord["wholesale_price"])-1,4)*100;

		$theinput = new inputPercentage("markup", $markup, "mark-up",2);
		$theinput->setAttribute("size","10");
		$theform->addField($theinput);

		$theinput = new inputField("reduction_price", $therecord["reduction_price"], "reduction amount" ,true);
		$theform->addField($theinput);

		$theinput = new inputPercentage("reduction_percent", $therecord["reduction_percent"], "reduction percent" ,true);
		$theinput->setAttribute("size","10");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
        //==============================================================
        //End Form Elements

    include("header.php");

?><div class="bodyline">
    <?php $theform->startForm($pageTitle);?>

    <div id="rightsideDiv">
<!--        <button type="button" id="updateWebsite" class="Buttons">update website</button> -->

	<fieldset id="fsAttributes">
		<legend>attributes</legend>
	          <p><br/><?php $theform->showField("inactive")?></p>
	          <p><br/><?php $theform->showField("styleid")?></p>
	          <p><br/><?php $theform->showField("site")?></p>
	</fieldset>

    </div>

    <div id="leftDiv">

        <fieldset>
            <legend>identification</legend>

            <p class="big"><?php $theform->showField("stylename")?></p>

        </fieldset>

        <fieldset>
                <legend>price / cost</legend>


                <p class="costsP"><?php $theform->showField("price")?> = </p>

                <p class="costsP"><?php $theform->showField("wholesale_price")?> + </p>

                <p class="costsP"><?php $theform->showField("markup")?></p>

                <p>
                    <br />
        <button type="button" id="updatePrice" class="Buttons">calculate price</button>
                </p>

        </fieldset>

        <fieldset>
                <legend>sale / promotion</legend>

                <p class="costsP"><?php $theform->showField("reduction_price")?> OR</p>
                <p class="costsP"><?php $theform->showField("reduction_percent")?></p>

        </fieldset>

        <fieldset>
            <legend>web</legend>

            <p><?php $theform->showField("description")?></p>

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

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
