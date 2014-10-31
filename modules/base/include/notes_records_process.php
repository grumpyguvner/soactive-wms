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

if(isset($_POST["command"])) {

	//convert any checked records to an array of ids
	foreach($HTTP_POST_VARS as $key=>$value){
		if (substr($key,0,5)=="check") $theids[]=$value;
	}

	//Search Options Command Process
	//=====================================================================================================
	switch($command) {
	case "new":
		// relocate to new screen
		//=====================================================================================================
		$theurl=getAddEditFile($db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1")."?reftable=".$reftable."&refid=".$_GET["refid"]."&backurl=".$backurl;
		goURL($theurl );
	break;
	case "delete":
		//a bit more complicated so we'll put it in it's own function?
		//=====================================================================================================

			include_once("modules/base/include/notes.php");
			
			$searchFunctions = new notesSearchFunctions($db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1",$theids);

			$statusmessage = $searchFunctions->delete_record();

	break;
	case "edit/view":
		// relocate to edit screen
		//=====================================================================================================
		  goURL(getAddEditFile($db,"tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1")."?id=".$theids[0]."&refid=".$_GET["refid"]."&backurl=".$backurl);
	break;
	}//end switch
} //end if
?>
