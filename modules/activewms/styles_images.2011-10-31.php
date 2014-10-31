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

	$pageTitle="Style ".$therecord["stylenumber"]." ".$therecord["stylename"];

	$phpbms->cssIncludes[] = "pages/activewms/styles.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/style_webdetails.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->enctype = "multipart/form-data";

                $theinput = new inputField("stylenumber",$therecord["stylenumber"],"style number",true);
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "hidden");
		$theform->addField($theinput);

		$theinput = new inputField("image_folder",$therecord["image_folder"],"image folder");
		$theform->addField($theinput);
		$theinput = new inputField("main_image",$therecord["main_image"],"main image");
		$theform->addField($theinput);
		$theinput = new inputField("alt_image1",$therecord["alt_image1"],"alt. image 1");
		$theform->addField($theinput);
		$theinput = new inputField("alt_image2",$therecord["alt_image2"],"alt. image 2");
		$theform->addField($theinput);
		$theinput = new inputField("alt_image3",$therecord["alt_image3"],"alt. image 3");
		$theform->addField($theinput);
		$theinput = new inputField("alt_image4",$therecord["alt_image4"],"alt. image 4");
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("styles entry","tab:ef7d6f16-89a9-426e-b4d5-b587d6fbdb45",$therecord["id"]);?>
    <div class="bodyline">
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">
		<fieldset>
			<legend>images</legend>
                        <a href="<?php echo $therecord["image_folder"].$therecord["main_image"] ?>" target="_blank"><img id="thumbpic" src="<?php echo $therecord["image_folder"].$therecord["main_image"] ?>" style="width: 76px; height: 102px; border: 1px solid black; display: block; margin: 3px;;" /></a>
                        <a href="<?php echo $therecord["image_folder"].$therecord["alt_image1"] ?>" target="_blank"><img id="thumbpic" src="<?php echo $therecord["image_folder"].$therecord["alt_image1"] ?>" style="width: 76px; height: 102px; border: 1px solid black; display: block; margin: 3px;;" /></a>
                        <a href="<?php echo $therecord["image_folder"].$therecord["alt_image2"] ?>" target="_blank"><img id="thumbpic" src="<?php echo $therecord["image_folder"].$therecord["alt_image2"] ?>" style="width: 76px; height: 102px; border: 1px solid black; display: block; margin: 3px;;" /></a>
                        <a href="<?php echo $therecord["image_folder"].$therecord["alt_image3"] ?>" target="_blank"><img id="thumbpic" src="<?php echo $therecord["image_folder"].$therecord["alt_image3"] ?>" style="width: 76px; height: 102px; border: 1px solid black; display: block; margin: 3px;;" /></a>
                        <a href="<?php echo $therecord["image_folder"].$therecord["alt_image4"] ?>" target="_blank"><img id="thumbpic" src="<?php echo $therecord["image_folder"].$therecord["alt_image4"] ?>" style="width: 76px; height: 102px; border: 1px solid black; display: block; margin: 3px;;" /></a>
		</fieldset>

	</div>

	<div id="leftsideDiv">
		<fieldset>
			<legend>images</legend>
			<p><?php $theform->showfield("image_folder");?></p>

			<p><?php $theform->showField("main_image") ?></p>
			<p><?php $theform->showField("alt_image1") ?></p>
			<p><?php $theform->showField("alt_image2") ?></p>
			<p><?php $theform->showField("alt_image3") ?></p>
			<p><?php $theform->showField("alt_image4") ?></p>

			<p><?php $theform->showfield("stylenumber");?></p>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>
        </div>

	<?php $theform->showGeneralInfo($phpbms,$therecord);?>
</div>
</form>
<?php include("footer.php");?>
