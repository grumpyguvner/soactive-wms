<?php

	include("../../include/session.php");
	include_once("include/email_reader.php");

	$log = new phpbmsLog("Connecting to email inbox", "EMAIL CONN");

	$myConn=pop3_login("m.activewms.sheactive.net","bleepserver.activewms","St4rl!ght");
	if (!$myConn){
		$log = new phpbmsLog("unable to connect to email inbox", "EMAIL CONN");
		exit;
	}

$log = new phpbmsLog("Listing contents of email inbox", "EMAIL CONN");
echo "Listing contents of email inbox<br>";
	$msgList = pop3_list($myConn);
        $m = 0;

	foreach ($msgList as $msg){
echo "Message ".$msg["msgno"]."<br>";

            $message = mail_mime_to_array($myConn,$msg["msgno"]);

            foreach ($message as $part){
echo "**************************************************************************<br>";
//                print_r ($part);
                if (isset($part["filename"])){
echo "****************  FOUND FILE ATTACHEMENT ".$part["filename"]." ***********<br>";
                    $file = fopen($_SERVER['DOCUMENT_ROOT']."/modules/bleep_email/attachments/".$part["filename"],"w");
                    fwrite($file, preg_replace('/(\r\n|\r|\n)/s',"\n",$part["data"]));
                    fclose($file);
                }
                //imap_savebody ($myConn, $_SERVER['DOCUMENT_ROOT']."/modules/bleep_email/attachments/webupdates.tar.gz", $msg["msgno"], "2.2");
            }

        }

echo "End of message<br>";

echo "<br>TODO: MOVE MESSAGE TO PROCESSED FOLDER<br>";

        $m++;
        if ($m>1){
                $log = new phpbmsLog("number of messages greater than throttle!", "EMAIL CONN");
                exit;
        }

?>
