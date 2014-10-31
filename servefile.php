<?php 
/*
 $Rev: 702 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2010-01-01 15:14:57 -0700 (Fri, 01 Jan 2010) $
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

session_cache_limiter('private');
require_once("include/session.php");

if(isset($_GET["i"])) {
    if(isset($_GET["type"]))
	$querystatement="SELECT banner_image AS file, banner_image_type AS type, banner_image_name AS name, '' AS roleid FROM ".($_GET["type"])." WHERE id=".((integer)$_GET["i"]);
    else
	$querystatement="SELECT file,type,name,roleid FROM files WHERE id=".((integer)$_GET["i"]);
	@ $queryresult=$db->query($querystatement);
	if($queryresult) {
		if($db->numRows($queryresult)){
			$therecord=$db->fetchArray($queryresult);
			if(hasRights($therecord["roleid"])){
				header("Content-type: ".$therecord["type"]);
				header("Content-Disposition: attachment; filename=\"".rawurlencode($therecord["name"])."\"");
			
				echo $therecord["file"];
			}
		}
	}
}
?>