<?php
/*
 $Rev: 704 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2010-01-01 23:10:02 -0700 (Fri, 01 Jan 2010) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

	$displayTable= new displaySearchTable($db);
	$displayTable->base = $base;
	$displayTable->initialize("tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1");
	$displayTable->querywhereclause=$whereclause;
	$displayTable->tableoptions["printex"]=0;
	$displayTable->tableoptions["othercommands"]=false;
	$displayTable->tableoptions["select"]=0;

	if(isset($_POST["deleteCommand"]))
		if($_POST["deleteCommand"]) $_POST["command"]=$_POST["deleteCommand"];

	if(isset($_POST["command"])){
		switch($_POST["command"]){
			case $displayTable->thetabledef["deletebutton"]:
			//=====================================================================================================

			include_once("modules/base/include/notes.php");

			$theids=explode(",",$_POST["theids"]);

			$searchFunctions = new notesSearchFunctions($db,$displayTable->thetabledef["uuid"],$theids);

			$tempmessage = $searchFunctions->delete_record();
			if($tempmessage) $statusmessage=$tempmessage;

			break;
		}//end switch
	}

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
	$phpbms->jsIncludes[] = "common/javascript/queryfunctions.js";
	$phpbms->topJS[] = 'xtraParamaters="backurl="+encodeURIComponent("'.$backurl.'")+String.fromCharCode(38)+"tabledefid='.urlencode($reftableid).'"+String.fromCharCode(38)+"refid='.urlencode($refuuid).'";';

	include("header.php");

	$phpbms->showTabs($tabgroup,$selectedtabid,$_GET["id"]);?><div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>
	<div>
		<form name="search" id="search" action="<?php echo htmlentities($_SERVER["REQUEST_URI"])?>" method="post" onsubmit="setSelIDs(this);return true;">
		<input name="theids" type="hidden" value="" />
		<?php
			$displayTable->displayQueryButtons();

			$displayTable->displayResultTable();
		?>
		</form>
	</div>
</div>
<?php include("../../footer.php"); }//endif?>
