<?php

//uncomment for DEBUG purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");
include_once("include/email_reader.php");

class importBleepEmails{

	var $db;
	var $mailbox;

        var $hostname="localhost";
        var $username="";
        var $password="";

        var $attachDir="/home/activewms/public_html/modules/bleep_email/attachments/";
        var $requiredFiles = "ALSTYLES.CSV BRIGHTON.CSV CGARDEN.CSV COLOUR.CSV DEPTS.CSV ECWAREHOUSEDB.CSV GROUPS.CSV PRODUCTS.CSV SIZE.CSV STYLES.CSV supplier.CSV WEBSTORE.CSV WHSE.CSV";

        var $scriptDir="/home/activewms/public_html/modules/bleep_email/scripts/";
        var $runScripts = "import-files.sh stock-adjustments.sh";
//        var $runScripts = "send-files-to-netro.sh";

        function importBleepEmails($db){
		$this->db = $db;

                //$log = new phpbmsLog("Connecting to email inbox", "BLEEP_EMAIL");


                if(defined("BLEEP_EMAIL_HOSTNAME"))
                        $this->hostname = BLEEP_EMAIL_HOSTNAME;
                if(defined("BLEEP_EMAIL_USER"))
                        $this->username = BLEEP_EMAIL_USER;
                if(defined("BLEEP_EMAIL_PASSWORD"))
                        $this->password = BLEEP_EMAIL_PASSWORD;
                if(defined("BLEEP_ATTACHMENT_DIR"))
                        $this->attachDir = BLEEP_ATTACHMENT_DIR;

                $this->mailbox=pop3_login($this->hostname,$this->username,$this->password);
                if (!$this->mailbox){
                        $log = new phpbmsLog("unable to connect to email inbox", "BLEEP_EMAIL");
                        exit;
                }

	}//end method --importBleepEmails--

	//This method should import new and updated records
	//from the netro mailbox
	function processMailbox(){

                //$log = new phpbmsLog("Retrieving messages in inbox", "BLEEP_EMAIL");
                $msgList = pop3_list($this->mailbox);

                foreach ($msgList as $msg){

                    $message = mail_mime_to_array($this->mailbox,$msg["msgno"]);

                    foreach ($message as $part){
                        if (isset($part["filename"])){
                            //$log = new phpbmsLog("Found file: ".$part["filename"], "BLEEP_EMAIL");
                            $file = fopen($this->attachDir.$part["filename"],"w");
                            if ($file){
                                if (fwrite($file, preg_replace('/(\r\n|\r|\n)/s',"\n",$part["data"]))){
                                    fclose($file);
                                }else{
                                    $log = new phpbmsLog("Error whilst writing to ".$part["filename"], "BLEEP_EMAIL");
                                }
                            }else{
                                $log = new phpbmsLog("Error unable to open ".$part["filename"], "BLEEP_EMAIL");
                            }


                        }
                    }

                    //move processed message to trash
                    if (!pop3_dele($this->mailbox,$msg["msgno"])){
                        $log = new phpbmsLog("Error unable to delete message", "BLEEP_EMAIL");
                    }

                }

                @imap_close($this->mailbox);
                $success = true;
                return ($success);

        } //end method --processMailbox--

	//Checks to see if all required files are present before importing them
	function requiredFilesExist(){

                $allFilesFound = true;

                $files = explode(" ", $this->requiredFiles);

                foreach ($files as $filename){
                    if (!file_exists($this->attachDir.$filename)) {
                        $allFilesFound = false;
                    }
                }

                return ($allFilesFound);

        } //end method --requiredFilesExist--

	//Import files into the database
	function processScripts(){

                $success = true;

                $scripts = explode(" ", $this->runScripts);

                foreach ($scripts as $script){
                    if ($success) {
                        if (file_exists($this->scriptDir.$script)) {
                            $log = new phpbmsLog("Attempting to execute $script", "BLEEP_EMAIL");

                            // Execute the shell command
                            //$shellOutput = shell_exec($this->scriptDir.$script.' > /dev/null; echo $?');
                            ob_start();
                            passthru($this->scriptDir.$script);
                            $shellOutput = ob_get_contents();
                            ob_end_clean(); //Use this instead of ob_flush()

                            //return execute status;
                            $log = new phpbmsLog("$script exit status (".trim($shellOutput).")", "BLEEP_EMAIL");
                        }else{
                            $success = false;
                            $log = new phpbmsLog("$script not found!", "BLEEP_EMAIL");
                        }
                    }
                }

                return ($success);

        } //end method --requiredFilesExist--

}//end class --importBleepEmails--

if(!isset($noOutput) && isset($db)){

    $clean = new importBleepEmails($db);
    $clean->processMailbox();
    if ($clean->requiredFilesExist()){
        //$log = new phpbmsLog("All files exist, starting import (using shell_exec with existing scripts)...", "BLEEP_EMAIL");
        //do something with them;
        if ($clean->processScripts()){
          $log = new phpbmsLog("All scripts have been run...", "BLEEP_EMAIL");
        }
    }

}//end if

?>