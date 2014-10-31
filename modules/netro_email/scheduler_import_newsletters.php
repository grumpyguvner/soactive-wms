<?php
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;


include("../../include/session.php");
include_once("include/email_reader.php");

class importNetroNewsletters{

	var $db;
	var $mailbox;

	function importNetroNewsletters($db){
		$this->db = $db;

		$log = new phpbmsLog("Connecting to email inbox", "Import Netro Newsletter Emails");
	
		$this->mailbox=pop3_login("m.sheactive.net","newsletters.sheactive","5h3Slne");
		if (!$this->mailbox){
			$log = new appError(0, "unable to connect to email inbox", "Import Netro Newsletter Emails");
		}
	}//end method --importNetroNewsletters--

	//This method should import new and updated records
	//from the netro mailbox
	function processMailbox(){

//		$logError = new appError(0, "Error Details", "Import Netro Emails"); //also stops execution!

//		$message = "Import Netro Emails: Hello World (number)";
//		$log = new phpbmsLog($message, "SCHEDULER");

                $this->writeLogfile("Listing contents of email inbox");
                $msgList = pop3_list($this->mailbox);
                $m = 0;

                foreach ($msgList as $msg){
                    $this->writeLogfile("Message ".$msg["msgno"]);

                    $header = imap_fetch_overview($this->mailbox,$msg["msgno"]);
                    $body = imap_fetchbody($this->mailbox,$msg["msgno"],"1.1");
                    if ($body == "") {
                            $body = imap_fetchbody($this->mailbox,$msg["msgno"], "1");
                    }

                    $lines = explode("\n",$body);
                    $post_data = array();

                    foreach ($lines as $ln){
                        $lin=imap_qprint($ln);
                        $this->writeLogfile($lin);
                    }

                    $post_data["TX"] = $header[0]->subject;
                    $post_data["type"] = "UK Newsletter";
                    $post_data["email"] = $header[0]->from;
                    $post_data["requesteddate"] = $header[0]->date;

                    $this->writeLogfile("*******************************************************************");
                    $this->writeLogfile('Processing Subscription '.$post_data["email"].' / '.$post_data["TX"]);
                    $this->writeLogfile("*******************************************************************");

                    $return = $this->postData($post_data);
                    if (!$return){
                        $log = new phpbmsLog("Problem posting subscription.", "NEWSLETTER");
                        $this->writeLogfile("Problem posting subscription.");
                        $this->writeLogfile($return);
                        
                        imap_mail_move($this->mailbox,$msg["msgno"],"ERRORS");

                    }else{
                        if (!imap_delete($this->mailbox,$msg["msgno"])){
                            $log = new phpbmsLog("Unable to delete message!", "NEWSLETTER");
                            $this->writeLogfile("Unable to delete message!");
                            exit;
                        }
                    }

                    imap_expunge($this->mailbox);

                    $m++;

                    if ($m>49){
                        $log = new phpbmsLog("number of messages greater than throttle!", "NEWSLETTER");
                        $this->writeLogfile("number of messages greater than throttle!");
                        exit;
                    }
                }


	}//end method --importNetroNewsletters-

    function postData($postData){
            //add the login details if necessary
            if (!isset($postData["phpbmsusername"])){
//                $postData["phpbmsusername"] = Configuration::get('ACTIVEWMS_USER');
//                $postData["phpbmspassword"] = Configuration::get('ACTIVEWMS_PASS');
                $postData["phpbmsusername"] = "netro42";
                $postData["phpbmspassword"] = "St4rl!ght";
            }

            $curl = curl_init();

//            curl_setopt($curl, CURLOPT_URL, 'http://'.Configuration::get('ACTIVEWMS_ADDR').'/modules/activewms/api_reservestock.php');
            curl_setopt($curl, CURLOPT_URL, 'http://warehouse.sheactive.co.uk/modules/activewms/api_newsletter.php');
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                $log = new phpbmsLog("ERROR: ".curl_error($curl), "NEWSLETTER");
                $this->writeLogfile("ERROR: ".curl_error($curl));
                return false;
            } else {
                curl_close($curl);
            }

//$log = new phpbmsLog("RETURN: ".($result), "NEWSLETTER");
            if (substr($result,0,2)=='OK'){
                //If export was ok then we should have the uuid from activewms
                return substr($result,2);
            }

            return false;
    }//end method --postData--

        function writeLogfile($message){

            $filename = dirname(__FILE__)."/log/newsletter.txt";
            $message = date("d/m/Y H:i:s",time()).": ".$message."\n";

            // Let's make sure the file exists and is writable first.
            if (is_writable($filename)) {
                if (!$handle = fopen($filename, 'a')) {
                     $log = new phpbmsLog("Cannot open file ($filename)", "NEWSLETTER");
                     exit;
                }

                // Write $message to our opened file.
                if (fwrite($handle, $message) === FALSE) {
                    $log = new phpbmsLog("Cannot write to file ($filename)", "NEWSLETTER");
                    exit;
                }

                fclose($handle);

            } else {
                $log = new phpbmsLog("The file ($filename) is not writable", "NEWSLETTER");
            }
        }

}//end class --importNetroNewsletters--

if(!isset($noOutput) && isset($db)){

    $clean = new importNetroNewsletters($db);
    $clean->processMailbox();

}//end if
?>
