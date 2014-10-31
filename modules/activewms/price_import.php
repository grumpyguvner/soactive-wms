<?php

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/imports.php");
	include("include/parsecsv.lib.php");

    if(!isset($_GET["id"]))
        exit;

	if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$tabledefid = mysql_real_escape_string($_GET["id"]);

	$querystatement = "
	    SELECT
            `modules`.`name` AS `modulename`,
            `tabledefs`.`maintable` AS `maintable`
	    FROM
            `tabledefs` INNER JOIN `modules` ON `tabledefs`.`moduleid` = `modules`.`uuid`
	    WHERE
            `tabledefs`.`uuid` = '".$tabledefid."'";

	$queryresult = $db->query($querystatement);

	$thereturn = $db->fetchArray($queryresult);

	//try to include table specific functions
        $tableFile = "../".$thereturn["modulename"]."/include/".$thereturn["maintable"].".php";

	if(file_exists($tableFile))
	    include_once($tableFile);

	//next, see if the table class exists
	if(class_exists($thereturn["maintable"])){

            $classname = $thereturn["maintable"];
            $thetable = new $classname($db,$tabledefid, $backurl);

	} else
            $thetable = new phpbmsTable($db,$tabledefid, $backurl);

	//finally, check to see if import class exists
	if(class_exists($thereturn["maintable"]."PriceImport")){

            $classname = $thereturn["maintable"]."PriceImport";
            $import = new $classname($thetable);

	} else
		$import = new phpbmsImport($thetable);

	//Next we process the form (if submitted) and
	// return the current record as an array ($therecord)
	// or if this is a new record, it returns the defaults
	$therecord = $import->processImportPage();

	//make sure that we set the status message id the processing returned one
	// (e.g. "Record Updated")
	if(isset($therecord["phpbmsStatus"]))
            $statusmessage = $therecord["phpbmsStatus"];

	$pageTitle = ($therecord["title"])?$therecord["title"]:"Price Import";

	$phpbms->cssIncludes[] = "pages/imports.css";


		//Form Elements
		//==============================================================

		// Create the form
		$theform = new importForm();
		$theform->enctype = "multipart/form-data";

		// lastly, use the jsMerge method to create the final Javascript formatting
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<!--
		Next we start the form.  This also prints the H1 with title, and top save,cancel buttons
		If you need to have other buttons, or need a specific top, you will need to create your form manually.
	-->
	<?php $theform->startForm($pageTitle, $import->pageType, count($import->transactionRecords))?>

	<div id="leftSideDiv">
		<!-- /* This next input is to store the temporary mysql table used for the confirmation insert */ -->
		<input id="tempFileID" name="tempFileID" type="hidden" value="<?php echo $import->tempFileID?>" />
		<!-- /* This next input is to determine the action of the cancel button (i.e. whether to redirect to backurl or not)*/ -->
		<!-- /* This next input also determines whether the file/import fieldset will be displayed or if the preview sections will be displayed*/ -->
		<input id="pageType" name="pageType" type="hidden" value="<?php echo $import->pageType?>" />

		<?php
		if($import->pageType == "main"){ ?>
		<fieldset >
			<legend>import</legend>

			<div id="uploadlabel">
				<p>
					<label for="import">file</label><br />
					<input id="import" name="import" type="file" size="64"/><br/>
				</p>


				<div id="info0" class="info">
					<p>
						For any file that is a comma seperated value (csv) file:
					</p>
					<p>
						The first row of your csv file should be the field-names of the table(s)
						that you wish to import to.  Additional lines will be the actual data
						that will be imported.
					</p>
					<p>
						When entering in currency, dates, or times use the format in the WMS's configuration
						(e.g. use English, US style dates if that is what the WMS is configured to).
					</p>

				</div>
			</div>

		</fieldset>
		<?php
		}//end if

		if($import->error && $import->pageType != "main"){
			?>
			<h2>Import Errors</h2>
			<div id="importError">
				<ul>
				<?php echo $import->error ?>
				</ul>
			</div>
			<?php
		}//end if
		$import->displayTransaction($import->transactionRecords,$import->table->fields);
	?>
	</div>
	<div id="createmodifiedby" >
	<?php
		//Last, we show the create/modifiy with the bottom save and cancel buttons
		// and then close the form.
		$theform->showButtons(2, $import->pageType, count($import->transactionRecords));
		?></div><?php
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
