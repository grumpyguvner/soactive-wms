<?php

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/files.php");

	if(!isset($_GET["backurl"])){
		$thetable = new files($db, "tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475");

		$pageTitle="File";
	} else {
		include("include/attachments.php");

		$backurl=$_GET["backurl"];
		if(isset($_GET["refid"]))
		$backurl.="?refid=".$_GET["refid"];

		$thetable = new attachments($db, "tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475",$backurl);

		$pageTitle="File Attachment";
	}

	$therecord = $thetable->processAddEditPage();

        if(!hasRights($therecord["roleid"]))
            goURL("../../noaccess.php");

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


	function getAttachments($db,$uuid){
		$querystatement = "
			SELECT
				`tabledefs`.`displayname`,
				`attachments`.`recordid`,
				`attachments`.`creationdate`,
				`tabledefs`.`editfile`
			FROM
				`attachments`INNER JOIN `tabledefs` ON `attachments`.`tabledefid`=`tabledefs`.`uuid`
			WHERE `attachments`.`fileid`='".$uuid."'
			";
		$queryresult=$db->query($querystatement);

		return $queryresult;
	}

	$phpbms->cssIncludes[] = "pages/files.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/file.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->enctype = "multipart/form-data";

		if(isset($therecord["id"])){
			$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,64,128);
			$theinput->setAttribute("class","important");
			$theform->addField($theinput);
		}

                $theinput = new inputField("type", $therecord["type"], "file type", false, null, 25);
                $theinput->setAttribute("readonly", "readonly");
                $theinput->setAttribute("class", "uneditable");
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
		$theform->addField($theinput);

		if(isset($_GET["tabledefid"]) && !isset($therecord["id"])){

			$theinput = new inputSmartSearch($db, "fileid", "Pick File", "", "exisiting file", false, 40);
			$theform->addField($theinput);

		}//end if

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<fieldset id="fsAttributes">
		<legend>Attributes</legend>
		<p id="roleidP"><?php $theform->showField("roleid")?></p>

                <p><?php $theform->showField("type"); ?></p>

	</fieldset>

	<div id="leftSideDiv">
	<fieldset>
		<legend>file</legend>
		<?php if(isset($_GET["tabledefid"])){?>
			<input id="attachmentid" name="attachmentid" type="hidden" value="<?php echo $therecord["attachmentid"]?>" />
			<input id="tabledefid" name="tabledefid" type="hidden" value="<?php echo $_GET["tabledefid"]?>" />
			<input id="recordid" name="recordid" type="hidden" value="<?php echo (integer) $_GET["refid"]?>" />
		<?php }?>

		<?php if($therecord["id"]) {?>
			<p>
				<button  type="button" class="Buttons" onclick="document.location='../../servefile.php?i=<?php echo $therecord["id"]?>'">View/Download <?php echo $therecord["name"] ?></button>
			</p>
			<p class="big">
				<?php $theform->showField("name")?><br />
				<span class="notes">If the file name does <strong>not</strong> include an extension your browser may not be able to download/view the file correctly.</span>
			</p>
			<p>
				<label for="upload">replace file</label><br />
				<input id="upload" name="upload" type="file" size="64" tabindex="260" />
			</p>
		<?php } else {?>
			<?php if(isset($_GET["tabledefid"])){?>
				<p><br />
					<input class="radiochecks" type="radio" name="newexisting" id="newfile" value="new"  checked="checked" onclick="switchFile()" /><label for="newfile">new file</label>&nbsp;&nbsp;
					<input type="radio"  class="radiochecks" name="newexisting" id="existingfile" value="existing" onclick="switchFile()" /><label for="existingfile">existing file</label><br />
					<span class="notes">Choose "existing file" if the file has already been uploaded into phpBMS.</span>
				</p>
				<div class="fauxP" id="fileidlabel">
					<?php $theform->showField("fileid");?>
				</div>
			<?php }?>
				<p id="uploadlabel">
					<label for="upload">upload new file</label><br />
					<input id="upload" name="upload" type="file" size="64" tabindex="260" />
				</p>
		<?php } ?>
		<p id="descriptionlabel">
			<label for="content">description</label><br />
			<textarea name="description" cols="45" rows="4" id="content"><?php echo htmlQuotes($therecord["description"])?></textarea>
		</p>
	</fieldset>
	<?php
	if($therecord["id"]) {
		$attchmentsquery=getAttachments($db,$therecord["uuid"]);
		if($db->numRows($attchmentsquery)){
		?>
		<h2>Record Attachments</h2>
		<div class="fauxP">
		<div style="" class="smallQueryTableHolder">
		<table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
			<tr>
				<th align="left">table</th>
				<th align="left" nowrap="nowrap" width="99%">ID</th>
				<th align="right" nowrap="nowrap">attached</th>
				<th align="left" nowrap="nowrap">&nbsp;</th>
			</tr>
		<?php
			while($attachmentrecord=$db->fetchArray($attchmentsquery)){
	?>
			<tr>
				<td nowrap="nowrap"><?php echo $attachmentrecord["displayname"] ?></td>
				<td><?php echo $attachmentrecord["recordid"] ?></td>
				<td align="right" nowrap="nowrap"><?php echo formatFromSQLDatetime($attachmentrecord["creationdate"]) ?></td>
				<td>
					<button class="graphicButtons buttonEdit" type="button" onclick="document.location='<?php echo APP_PATH.$attachmentrecord["editfile"]."?id=".$attachmentrecord["recordid"] ?>'"><span>edit</span></button>
				</td>
			</tr>
	<?php
			} ?></table></div></div><?php
		}
	}?>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
