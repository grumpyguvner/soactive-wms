<?php
/*
 $Rev: 703 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2010-01-01 17:34:45 -0700 (Fri, 01 Jan 2010) $
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
	require_once("../../include/session.php");
	require_once("../../include/fields.php");
	require_once("include/snapshot_include.php");

	//Page details;
	$headingTitle = formatVariable(trim($_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"])."'s Snapshot");
	$pageTitle = APPLICATION_NAME." - ".$headingTitle;

	$phpbms->cssIncludes[] = "pages/base/snapshot.css";

	$phpbms->jsIncludes[] = "modules/base/javascript/snapshot.js";

	$snapshot = new snapshot($db);

	if(isset($_POST["cmd"]))
		$snapshot->process($_POST);

	$snapshot->getWidgets();

	$phpbms->jsIncludes = $snapshot->merge($phpbms->jsIncludes, "jsIncludes");
	$phpbms->cssIncludes = $snapshot->merge($phpbms->cssIncludes, "cssIncludes");

	require("header.php");

?>
<div class="bodyline">

	<h1><?php echo $headingTitle;?></h1>

	<?php

		$snapshot->displaySystemMessages();
		$snapshot->displayWidgets();

	?>

</div>

<?php include("footer.php")?>
