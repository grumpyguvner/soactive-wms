<?php
/////////////////////////////////////////////
//   IMPORT NEWSLETTER SUBSCRIPTIONS       //
/////////////////////////////////////////////
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");
include("include/tables.php");

//$now = gmdate('Y-m-d H:i', strtotime('now'));

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

class api_newsletter{

	function api_newsletter($db){
		$this->db = $db;
	}//end method --api_newsletter--

	//This method should import new and updated records
	//from the edi database
	function importSubscription($postData){

//          $logError = new appError(0, "Error Details", "EDI SUBSCRIPTION UPDATE"); //also stops execution!

//          $message = "EDI SUBSCRIPTION UPDATE: Hello World (number)";
//          $log = new phpbmsLog($message, "EDI SUBSCRIPTION UPDATE", NULL, $this->db);

            $message = "New subscription received from ".$postData["email"];
            $message .=" on ".$postData["requesteddate"];

            $log = new phpbmsLog($message, "EDI SUBSCRIPTION UPDATE", NULL, $this->db);

            $this->updateNewsletterSubscription($postData["email"], $postData["requesteddate"], $postData["type"], $postData["TX"]);

            //If everything ok then return uuid
//            echo "OK".$therecord["uuid"];
            echo "OK1234";

	}//end method --importSubscription-

    function updateNewsletterSubscription($email, $requested, $type, $newStatus = "SUBSCRIBE"){

        $message = "";
        $querystatement='INSERT INTO `subscriptions_updates` (`email`,`requested`,`type`,`status`)
                          VALUES (\''.$email.'\',\''.$requested.'\',\''.$type.'\',\''.$newStatus.'\');';
        $queryresult = $this->db->query($querystatement);
        if(!$queryresult){
                $error= new appError(0,"Could Not Insert Subscription Record","EDI SUBSCRIPTION UPDATE");
        }

    }//end function updateNewsletterSubscription


}//end class --api_newsletter--

if(!isset($noOutput) && isset($db)){

    $clean = new api_newsletter($db);
    switch ($_POST["TX"]){
	case "unsubscribe":
	case "subscribe":
		$clean->importSubscription($_POST);
	break;
    }

}//end if
?>