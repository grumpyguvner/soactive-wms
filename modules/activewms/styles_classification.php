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

	$phpbms->cssIncludes[] = "pages/activewms/styles_classification.css";
	$phpbms->jsIncludes[] = "modules/activewms/javascript/styles_classification.js";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();
        $theform->enctype = "multipart/form-data";

        $theinput = new inputField("stylenumber",$therecord["stylenumber"],"style number",true);
        $theinput->setAttribute("readonly", "readonly");
//        $theinput->setAttribute("class", "hidden");
        $theinput->setAttribute("display", "none");
        $theform->addField($theinput);

        $theinput = new inputField("stylename",$therecord["stylename"],"name");
        $theinput->setAttribute("readonly", "readonly");
        $theinput->setAttribute("display", "none");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputSmartSearch($db, "moresports", "Pick Sport For Style", "", "sport");
        $theform->addField($theinput);

        $theinput = new inputDataTableList($db, "default_sportid", $therecord["default_sportid"], "sports", "uuid", "name",
                                                        "inactive=0", "name", true, "default sport", true, "");
        $theform->addField($theinput);

        $theinput = new inputSmartSearch($db, "morecategories", "Pick Category For Style", "", "category");
        $theform->addField($theinput);

        $theinput = new inputDataTableList($db, "default_categoryid", $therecord["default_categoryid"], "categories", "uuid", "name",
                                                        "inactive=0", "name", true, "default category", true, "");
        $theform->addField($theinput);

        $thetable->getCustomFieldInfo();
        $theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
        $theform->jsMerge();
        //==============================================================
        //End Form Elements

	include("header.php");
?>
<form action="<?php echo htmlentities($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("styles entry","tab:d3e114ff-1e61-57aa-f0a3-5e485e2a4246",$therecord["id"]);?>
    <div class="bodyline">
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">

                <fieldset>
                    <legend>Categories</legend>
                    <p>
                        <?php $theform->showField("morecategories")?>
                        <button type="button" id="addCategoryButton" class="graphicButtons buttonPlus" title="Add Category"><span>+</span></button>
                    </p>

                    <?php $thetable->displayAdditionalCategories($therecord["addcategories"]) ?>

		    <p><?php $theform->showField("default_categoryid") ?></p>

                </fieldset>

	</div>

	<div id="leftsideDiv">

                <fieldset>
                    <legend>Sports</legend>
                    <p>
                        <?php $theform->showField("moresports")?>
                        <button type="button" id="addSportButton" class="graphicButtons buttonPlus" title="Add Sport"><span>+</span></button>
                    </p>

                    <?php $thetable->displayAdditionalSports($therecord["addsports"]) ?>

		    <p><?php $theform->showField("default_sportid") ?></p>

                </fieldset>

	</div>

        <p style="display: none;">
                <?php $theform->showfield("stylenumber");?>
                <?php $theform->showfield("stylename");?>
                <?php $theform->showfield("webenabled");?>
                <?php $theform->showfield("inactive");?>
        </p>

	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
	?>
</div>
</form>
<?php include("footer.php");?>
