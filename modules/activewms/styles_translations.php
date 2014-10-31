<?php

	include("../../include/session.php");
	require_once("../../include/search_class.php");
	include("include/styles_translations_class.php");

	if(isset($_GET["refid"])) $_GET["id"]=$_GET["refid"];
	if(!isset($_GET["id"])) $error = new appError(300,"Passed variable not set (id)");

	$styleTranslation = new styleTranslation($db, $_GET["id"]);

	$pageTitle = $styleTranslation->getPageTitle();

	// styles table definition uuid
	$reftableid = "tbld:7ecb8e4e-8301-11df-b557-00238b586e42";

  	$whereclause = "
		styleid = '".$styleTranslation->styleuuid."'";

	$backurl="../activewms/styles_translations.php";
	$base="../../";

	$displayTable= new displaySearchTable($db);
	$displayTable->base = $base;
	$displayTable->initialize("tbld:90f1243e-91ee-dfbc-96f1-2c07926bbfdb");
	$displayTable->querywhereclause = $whereclause;

	if(isset($_POST["deleteCommand"]))
		if($_POST["deleteCommand"]) $_POST["command"] = $_POST["deleteCommand"];

	if(!isset($_POST["othercommands"])) $_POST["othercommands"]="";
		if($_POST["othercommands"]) $_POST["command"]="other";

	if(isset($_POST["command"])){

		switch($_POST["command"]){

			case "delete":
				//=====================================================================================================

				$_POST["othercommands"] = -1;

			case "other":
				$displayTable->recordoffset=0;
				// process table specific commands (passed by settings)
				//=====================================================================================================
				$theids=explode(",",$_POST["theids"]);

//				include_once("modules/activewms/include/styletranslationtorecord.php");

				//next, see if the searchclass exists
				if(class_exists($displayTable->thetabledef["maintable"]."SearchFunctions")){
					$classname = $displayTable->thetabledef["maintable"]."SearchFunctions";
					$searchFunctions = new $classname($db,$displayTable->thetabledef["uuid"],$theids);
				} else
					$searchFunctions = new searchFunctions($db,$displayTable->thetabledef["uuid"],$theids);

				//grab the method name
				if(((int) $_POST["othercommands"]) === -1)
					$functionname = "delete_record";
				else {
					$querystatement = "SELECT name FROM tableoptions WHERE id=".((int) $_POST["othercommands"]);
					$queryresult = $db->query($querystatement);
					$therecord = $db->fetchArray($queryresult);
					$functionname = $therecord["name"];
				}

				if(method_exists($searchFunctions,$functionname))
					$statusmessage = $searchFunctions->$functionname();
				else
					$statusmessage = "Function ".$functionname." not defined";

			break;

			case "omit":
				// omit selected from current query
				//=====================================================================================================
				$displayTable->recordoffset=0;
				$tempwhere="";
				$theids=explode(",",$_POST["theids"]);
				foreach($theids as $theid){
					$tempwhere.=" or ".$displayTable->thetabledef["maintable"].".id=".$theid;
				}
				$tempwhere=substr($tempwhere,3);
				$displayTable->querywhereclause="(".$displayTable->querywhereclause.") and not (".$tempwhere.")";
			break;

			case "keep":
				// keep only those ids
				//=====================================================================================================
				$displayTable->recordoffset=0;
				$tempwhere="";
				$theids=explode(",",$_POST["theids"]);
				foreach($theids as $theid){
					$tempwhere.=" or ".$displayTable->thetabledef["maintable"].".id=".$theid;
				}
				$tempwhere=substr($tempwhere,3);
				$displayTable->querywhereclause=$tempwhere;
			break;

		}//end switch

	}//endif

	//on the fly sorting... this needs to be done after command processing or the querystatement will not work.
	if(!isset($_POST["newsort"])) $_POST["newsort"]="";
	if(!isset($_POST["desc"])) $_POST["desc"]="";

	if($_POST["newsort"]!="") {
		//$displayTable->setSort($_POST["newsort"]);
		foreach ($displayTable->thecolumns as $therow){
			if ($_POST["newsort"]==$therow["name"]) $therow["sortorder"]? $displayTable->querysortorder=$therow["sortorder"] : $displayTable->querysortorder=$therow["column"];
		}
		$_POST["startnum"]=1;
	} elseif($_POST["desc"]!="")  $displayTable->querysortorder.=" DESC";

	if($displayTable->querytype!="new" and $displayTable->querytype!="edit") {

		//record offset?
		if(isset($_POST["offset"])) if($_POST["offset"]!="") $displayTable->recordoffset=$_POST["offset"];

		$displayTable->issueQuery();

		$phpbms->cssIncludes[] = "pages/search.css";
		$phpbms->cssIncludes[] = "pages/activewms/styletranslations.css";
		$phpbms->jsIncludes[] = "common/javascript/queryfunctions.js";
		$phpbms->topJS[] = 'xtraParamaters="backurl="+encodeURIComponent("'.$backurl.'")+String.fromCharCode(38)+"tabledefid="+encodeURIComponent("'.$reftableid.'")+String.fromCharCode(38)+"refid="+encodeURIComponent("'.$styleTranslation->styleid.'");';

		include("header.php");

		$phpbms->showTabs("styles entry", "tab:625192d0-00e6-ae2c-5b8c-f433bbf6e546", ((int) $_GET["id"]));?><div class="bodyline">

			<h1 id="h1Title"><?php echo $pageTitle?></h1>

			<form name="search" id="search" action="<?php echo htmlentities($_SERVER["REQUEST_URI"])?>" method="post" onsubmit="setSelIDs(this);return true;">
<!--			<input name="command" id="reset" type="submit"/> -->
			<input name="theids" id="theids" type="hidden"  />
			<?php
//				$displayTable->displayQueryButtons();

				$displayTable->displayResultTable();
			?>
			</form>

		</div>
	<?php include("footer.php"); }//end if -> querytype?>
