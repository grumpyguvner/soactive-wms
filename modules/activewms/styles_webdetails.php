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
                //allow entry of style number if creating a new record
                if(!$therecord["stylenumber"]==""){
                    $theinput->setAttribute("readonly", "readonly");
                    $theinput->setAttribute("class", "uneditable");
                }
		$theform->addField($theinput);

		$theinput = new inputField("sizeguide",$therecord["description"],"description",false,NULL,96,255);
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("styles entry","tab:5c7b1155-d5d4-114d-f4b1-c01d4ac68240",$therecord["id"]);?>
    <div class="bodyline">
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">
		<fieldset>
			<legend>images</legend>


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
			<legend>web</legend>
			<p>
				<?php $theform->showfield("webenabled");?>
			</p>

			<p><?php $theform->showField("description") ?></p>

			<div id="webstuff">
				<p>
					<label for="keywords">keywords <span class="notes">(comma separated key word list)</span></label><br />
					<input type="text" id="keywords" name="keywords" value="<?php echo htmlQuotes($therecord["keywords"])?>" size="40" maxlength="255"/>
				</p>
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

				<div class="fauxP">
					thumbnail graphic<br />
					<?php if($therecord["thumbnailmime"]) {?>
						<img id="thumbpic" src="<?php echo APP_PATH ?>dbgraphic.php?t=styleThumb&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
					<?php } else {?>
						<div id="noThumb" class="tiny" align="center">no thumbnail</div>
					<?php } ?>
					upload thumbnail<br />
					<input type="hidden" id="thumbchange" name="thumbchange" value="" />
					<div id="thumbdelete" style="display:<?php if($therecord["thumbnailmime"]) echo "block"; else echo "none";?>"><button id="deleteThumbButton" type="button" class="Buttons">delete thumbnail</button></div>
					<div id="thumbadd" style="display:<?php if($therecord["thumbnailmime"]) echo "none"; else echo "block";?>"><input id="thumbnailupload" name="thumbnailupload" type="file" size="40"/></div>
				</div>

				<div class="fauxP">
					main picture<br />
					<?php if($therecord["picturemime"]) {?>
						<img id="picturepic" src="<?php echo APP_PATH ?>dbgraphic.php?t=stylePic&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
					<?php } else {?>
						<div id="noPicture" class="tiny" align="center">no picture</div>
					<?php } ?>
					upload picture <br />
					<input type="hidden" id="picturechange" name="picturechange" value="" />
					<div id="picturedelete" style="display:<?php if($therecord["picturemime"]) echo "block"; else echo "none";?>"><button id="deletePictureButton" type="button" class="Buttons">delete picture</button></div>
					<div id="pictureadd" style="display:<?php if($therecord["picturemime"]) echo "none"; else echo "block";?>"><input id="pictureupload" name="pictureupload" type="file" size="40" onchange="updatePictureStatus('picture','upload')" tabindex="270"/></div>
				</div>
			</div>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>


	<?php $theform->showGeneralInfo($phpbms,$therecord);?>
</div>
</form>
<?php include("footer.php");?>
