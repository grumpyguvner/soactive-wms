<?php

set_time_limit(0);
	$loginNoKick=true;
	$loginNoDisplayError=true;

	include("../../include/session.php");

//	$now = gmdate('Y-m-d H:i', strtotime('now'));

	//WE ARE ADDING THE SELECT HERE TO PREVENT OTHER SQL STATEMENTS BEING PASSED
	$querystatement = str_replace("\&#39;","\'",$_POST["data"]);
	$querystatement = str_replace("&#39;","'",stripslashes(" SELECT ".$querystatement));

$log = new phpbmsLog($querystatement, "EDI QUERY");

	$queryresult=$db->query($querystatement);

	if(!$queryresult){
		$error= new appError(0,"Could Not Perform Query","EDI Query");
	}

	while($record=$db->fetchArray($queryresult)){
	        foreach($record as $name => $field){
			//replace all tab and newline characters as these are
			//used to delimit fields and records.
			echo str_replace(chr(9)," ",str_replace(chr(10),'',nl2br($record[$name])))."\t";
		}//endforeach

		echo "\n";
	}//endwhile

?>
