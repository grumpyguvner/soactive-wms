<?php
/*
 $Rev: 311 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-10-02 19:51:27 -0600 (Tue, 02 Oct 2007) $
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
    class wdgta1aec114954b37c104747d4e851c728c extends widget{

        var $uuid ="wdgt:a1aec114-954b-37c1-0474-7d4e851c728c";
        var $type = "little";
        var $title = "Workload";
        var $jsIncludes = array('modules/base/widgets/workload/workload.js');
        var $cssIncludes = array('widgets/base/workload.css');

        var $phpbms;
        var $userid;
	var $useruuid;


        function displayMiddle(){

            global $phpbms;

	    $this->phpbms = $phpbms;
	    $this->userid = $_SESSION["userinfo"]["id"];
	    $this->useruuid = $_SESSION["userinfo"]["uuid"];

            $this->showTasks("GivenAssignments");

            $this->showTasks("ReceivedAssignments");

        }//end function showMiddle


	function showTasks($type){

		$querystatement="
			SELECT
				id,
				type,
				subject,
				completed,
				if(enddate < CURDATE(),1,0) AS ispastdue,
				if(assignedtodate < CURDATE(),1,0) AS ispastassigneddate,
				startdate,
				enddate,
				assignedtodate,
				private,
				assignedbyid,
				assignedtoid,
				IF(assignedtodate IS NOT NULL, assignedtodate, IF((enddate IS NOT NULL && type = 'TS'), enddate, IF((startdate IS NOT NULL && type = 'EV'), startdate, CURDATE()))) AS xdate
			FROM
				notes
			WHERE";

		switch($type){

			case "ReceivedAssignments":

				$querystatement.="
					((
						assignedtoid = '".$this->useruuid."'
						OR 	(
							type = 'TS'
							AND (assignedtoid = '' OR assignedtoid IS NULL)
							AND createdby = ".$this->userid."
							)
					)
						AND 	(
							completed = 0
							OR 	(
								completed = 1
								AND completeddate >= CURDATE()
								)
							)
					)";

				$title = "Assignments";
				$id = "AS";
				break;

			case "GivenAssignments":

				$querystatement.="
					(assignedbyid = '".$this->useruuid."'
					AND (completed = 0
						OR (completed = 1 AND completeddate >= CURDATE())
					))";

				$title = "Delegations";
				$id = "DG";
				break;

		}//endswitch


		$querystatement.="AND (
					(startdate IS NULL AND enddate IS NULL AND assignedtodate IS NULL)
					OR (startdate IS NOT NULL AND startdate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY) AND enddate IS NULL AND assignedtodate IS NULL)
					OR (enddate IS NOT NULL AND enddate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
					OR (assignedtodate IS NOT NULL AND assignedtodate <= DATE_ADD(CURDATE(),INTERVAL 30 DAY))
				   )";

		$querystatement.=" ORDER BY
				importance DESC,
				xdate,
				subject";

		$queryresult = $this->db->query($querystatement);

		$numRows = $this->db->numRows($queryresult);

		?>
		<h3 class="tasksLinks"><?php echo $title; if($numRows) {?> <span class="small">(<?php echo $numRows?>)</span><?php } ?></h3>

		<div class="tasksDivs">
			<div>

			<?php if($numRows){

				$linkStart = getAddEditFile($this->db,"tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1");
				$section["title"] = "Today";
				$section["date"] = mktime(0,0,0,date("m"),date("d"),date("Y"));

				while($therecord = $this->db->fetchArray($queryresult)) {

					$className="tasks";

					if($therecord["completed"])
						$className.=" complete";
					else if($therecord["ispastdue"] || $therecord["ispastassigneddate"])
						$className.=" pastDue";

					if($therecord["private"]) $className.=" private";

					$className.=" ".$therecord["type"];

					$checkBoxID = $id.$therecord["type"]."C".$therecord["id"];

					$link = $linkStart."?id=".$therecord["id"]."&amp;backurl=".APP_PATH."modules/base/snapshot.php";

					$rightSide = "";

					if($therecord["assignedtodate"])
						$rightSide .= "FUP: ".formatFromSQLDate($therecord["assignedtodate"])."<br />";

					switch($therecord["type"] ){

						case "TS":
							if($therecord["enddate"])
								$rightSide .= "Due: ".formatFromSQLDate($therecord["enddate"])."<br />";
							break;

						case "EV":
							$rightSide .= "Start: ".formatFromSQLDate($therecord["startdate"])."<br />";
							$rightSide .= "End: ".formatFromSQLDate($therecord["enddate"])."<br />";
							break;

					}//endswitch

					if(!$rightSide)
						$rightSide = "&nbsp;";

					$bottomInfo = "";

					switch($type){

						case "ReceivedAssignments":
							if($therecord["assignedbyid"])
								$bottomInfo = "Assigned By: ".htmlQuotes($this->phpbms->getUserName($therecord["assignedbyid"], true));
							break;

						case "GivenAssignments":
							$bottomInfo = "Assigned To: ".htmlQuotes($this->phpbms->getUserName($therecord["assignedtoid"], true));
							break;

					}//endswitch

					// Looking for grouping changes in headers (3 days, 4-7 days, > 7 days)
					$xdate = stringToDate($therecord["xdate"],"SQL") ;

					if($xdate > $section["date"]){

						while($xdate > $section["date"]){

							switch($section["title"]){

								case "Today":
									$section["title"] = "Soon";
									$section["date"] = mktime(0,0,0,date("m"),date("d")+7,date("Y"));
									break;

								case "Soon":
									$section["title"] = "Later";
									$section["date"] = mktime(0,0,0,1,1,2038);
									break;
								case "Later":
									//should never be here
									$section["date"] = $xdate;

							}//end switch

						}//endwhile

						?><div class="taskSection"><?php echo $section["title"] ?></div><?php

					}//end if

					?>

					<div id="<?php echo $id.$therecord["id"]?>" class="<?php echo $className?>">

						<span class="taskRight"><?php echo $rightSide ?></span>

						<input class="radiochecks taskChecks" id="<?php  echo $checkBoxID?>" name="<?php  echo $checkBoxID?>" type="checkbox" value="1" <?php if($therecord["completed"]) echo 'checked="checked"'?>  align="middle" />

						<a href="<?php echo $link?>"><?php echo htmlQuotes($therecord["subject"])?></a>

						<?php if($bottomInfo){ ?>

							<p><?php echo $bottomInfo ?></p>

						<?php }//endif ?>
					</div>

				<?php }//endwhile
				} else { ?>
					<p class="small disabledtext">no <?php echo strtolower($title)?></p><?php
				}?>
			</div>
		</div> <?php

	}//end method showTasks

    }//end class workload
?>
