<?php
set_time_limit(0);
$loginNoKick=true;
$loginNoDisplayError=true;

include("../../include/session.php");
include_once("include/email_reader.php");

class netro_emails{

    function postData($postData){
            //add the login details if necessary
            if (!isset($postData["activewmsuser"])){
//                $postData["phpbmsusername"] = Configuration::get('ACTIVEWMS_USER');
//                $postData["phpbmspassword"] = Configuration::get('ACTIVEWMS_PASS');
                $postData["phpbmsusername"] = "netro42";
                $postData["phpbmspassword"] = "St4rl!ght";
            }

            $curl = curl_init();

//            curl_setopt($curl, CURLOPT_URL, 'http://'.Configuration::get('ACTIVEWMS_ADDR').'/modules/activewms/api_reservestock.php');
            curl_setopt($curl, CURLOPT_URL, 'http://warehouse.sheactive.co.uk/modules/activewms/api_reservestock.php');
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                $log = new phpbmsLog("ERROR: ".curl_error($curl), "EMAIL CONN");
                $this->writeLogfile("ERROR: ".curl_error($curl));
                return false;
            } else {
                curl_close($curl);
            }

$log = new phpbmsLog("RETURN: ".($result), "EMAIL CONN");
            if (substr($result,0,2)=='OK'){
                //If export was ok then we should have the uuid from activewms
                return substr($result,2);
            }

            return false;
    }//end method --postData--

    function processEmails(){

        $log = new phpbmsLog("Connecting to email inbox", "EMAIL CONN");

//        $myConn=pop3_login("m.sheactive.net","sales.sheactive","5h3Act!ve");
        $myConn=pop3_login("m.sheactive.net","orders.sheactive","St4rl!ght");
        if (!$myConn){
                $log = new phpbmsLog("unable to connect to email inbox", "EMAIL CONN");
                exit;
        }

        $this->writeLogfile("Listing contents of email inbox");
        $msgList = pop3_list($myConn);
        $m = 0;

        foreach ($msgList as $msg){
                $this->writeLogfile("Message ".$msg["msgno"]);

                $body = imap_fetchbody($myConn,$msg["msgno"],"1.1");
                if ($body == "") {
                        $body = imap_fetchbody($myConn,$msg["msgno"], "1");
                }

                $lines = explode("\n",$body);
                $post_data = array();
                $variables = array();
                $section = "HEADER";
                $orderline = "";
                $orderlines = array();

                foreach ($lines as $ln){
                    $lin=imap_qprint($ln);
                    $this->writeLogfile($lin);

                    switch ($section) {
                            case "HEADER":
                                    if (substr($lin,0,28)=="An order has been placed at "){
                                            $myyear=trim(substr($lin,43,4));
                                            $mymonth=trim(substr($lin,40,2));
                                            $myday=trim(substr($lin,37,2));
                                            $mytime=trim(substr($lin,28,5));
                                            $post_data["orderdate"] = $myyear."-".$mymonth."-".$myday." ".$mytime.":00";
                                    }
                                    if (substr($lin,0,19)=="TRANSACTION DETAILS"){
                                            $section = "PAYMENT";
                                    }
                                    break;

                            case "PAYMENT":
                                    if (substr($lin,0,5)=="Cart:"){
                                            $post_data["leadsource"] = "www.sheactive.co.uk";
                                            $post_data["webconfirmationno"] = trim(substr($lin,6,strlen($lin)-6));
                                    }
                                    if (substr($lin,0,9)=="Protx ID:"){
                                            $variables["paymentmethod"] = "SAGEPAY";
                                            $variables["paymentreference"] = trim(substr($lin,10,strlen($lin)-10));
                                    }
                                    if (substr($lin,0,19)=="Transaction Result:"){
                                            $variables["paymentresult"] = trim(substr($lin,20,strlen($lin)-20));
                                    }
                                    if (substr($lin,0,18)=="Purchase currency:"){
                                            $variables["paymentcurrency"] = trim(substr($lin,19,strlen($lin)-19));
                                    }
                                    if (substr($lin,0,13)=="Total amount:"){
                                            $variables["paymenttotal"] = trim(substr($lin,14,strlen($lin)-14));
                                    }
                                    if (substr($lin,0,14)=="Authorisation:"){
                                            $variables["paymentauthorisation"] = trim(substr($lin,15,strlen($lin)-15));
                                    }
                                    if (substr($lin,0,21)=="fraud-related checks:"){
                                            $variables["fraudchecks"] = trim(substr($lin,22,strlen($lin)-22));
                                    }
                                    if (substr($lin,0,14)=="AddressResult:"){
                                            $variables["fraudcheckaddress"] = trim(substr($lin,15,strlen($lin)-15));
                                    }
                                    if (substr($lin,0,15)=="PostCodeResult:"){
                                            $variables["fraudcheckpostcode"] = trim(substr($lin,16,strlen($lin)-16));
                                    }
                                    if (substr($lin,0,10)=="CV2Result:"){
                                            $variables["fraudcheckcv2"] = trim(substr($lin,11,strlen($lin)-11));
                                    }
                                    if (substr($lin,0,16)=="CUSTOMER DETAILS"){
                                            $section = "CUSTOMER";
                                    }
                                    break;

                            case "CUSTOMER":
                                    if (substr($lin,0,5)=="NAME:"){
                                            $post_data["lastname"] = trim(substr(strrchr($lin, " "), 1));
                                            $post_data["firstname"] = trim(substr($lin,6,strlen($lin)-strlen($post_data["lastname"])-7));
                                    }
                                    if (substr($lin,0,8)=="ADDRESS:"){
                                            $post_data["billtoaddress1"] = trim(substr($lin,9,strlen($lin)-9));
                                    }
                                    if (substr($lin,0,5)=="TOWN:"){
                                            $post_data["billtocity"] = trim(substr($lin,6,strlen($lin)-6));
                                    }
                                    if (substr($lin,0,9)=="POSTCODE:"){
                                            $post_data["billtopostcode"] = trim(substr($lin,10,strlen($lin)-10));
                                    }
                                    if (substr($lin,0,8)=="COUNTRY:"){
                                            $post_data["billtocountry"] = trim(substr($lin,9,strlen($lin)-9));
                                    }
                                    if (substr($lin,0,6)=="PHONE:"){
                                            $post_data["billtotelephone"] = trim(substr($lin,7,strlen($lin)-7));
                                    }
                                    if (substr($lin,0,6)=="EMAIL:"){
                                            $post_data["billtoemail"] = trim(substr($lin,7,strlen($lin)-7));
                                    }
                                    if (substr($lin,0,9)=="DISCOUNT:"){
                                            $post_data["promocode"] = trim(substr($lin,10,strlen($lin)-10));
                                    }
                                    if (substr($lin,0,16)=="DELIVERY DETAILS"){
                                            $section = "DELIVERY";
                                    }
                                    break;

                            case "DELIVERY":
                                    if (substr($lin,0,5)=="NAME:"){
                                            $post_data["shiptoname"] = trim(substr($lin,6,strlen($lin)-6));
                                    }
                                    if (substr($lin,0,8)=="ADDRESS:"){
                                            $post_data["shiptoaddress1"] = trim(substr($lin,9,strlen($lin)-9));
                                    }
                                    if (substr($lin,0,5)=="TOWN:"){
                                            $post_data["shiptocity"] = trim(substr($lin,6,strlen($lin)-6));
                                    }
                                    if (substr($lin,0,9)=="POSTCODE:"){
                                            $post_data["shiptopostcode"] = trim(substr($lin,10,strlen($lin)-10));
                                    }
                                    if (substr($lin,0,8)=="COUNTRY:"){
                                            $post_data["shiptocountry"] = trim(substr($lin,9,strlen($lin)-9));
                                    }
                                    if (substr($lin,0,13)=="ORDER DETAILS"){
                                            $section = "ORDER DETAIL";
                                    }
                                    break;

                            case "ORDER DETAIL":
                                    if (substr($lin,0,5)=="=====" || trim($lin)==""){
                                            if ($orderline!=""){
                                                    $orderlines[]=$orderline;
                                                    $orderline="";
                                            }
                                            break;
                                    }
                                    $orderline=trim($orderline." ".str_replace("%20"," ",trim($lin)));
                                    if (substr($lin,0,10)=="SUB-TOTAL:"){
                                            $section = "ORDER SUMMARY";
//                                            $variables["ordersubtotal"] = trim(substr($lin,11,strlen($lin)-11));
                                            $variables["ordersubtotal"] = trim(substr($lin,15,strlen($lin)-15));
                                    }
                                    break;

                            case "ORDER SUMMARY":
                                    if (substr($lin,0,11)=="POSTAGE TO:"){
                                            $post_data["shippingregion"] = trim(substr($lin,12,strlen($lin)-12));
                                    }
                                    if (substr($lin,0,13)=="POSTAGE TYPE:"){
                                            $post_data["shippingmethod"] = trim(substr($lin,14,strlen($lin)-14));
                                    }
                                    if (substr($lin,0,13)=="POSTAGE COST:"){
//                                            $post_data["shipping"] = trim(substr($lin,14,strlen($lin)-14));
                                            $post_data["shipping"] = trim(substr($lin,15,strlen($lin)-15));
                                    }
                                    if (substr($lin,0,12)=="GRAND TOTAL:"){
//                                            $variables["ordertotal"] = trim(substr($lin,13,strlen($lin)-13));
                                            $variables["ordertotal"] = trim(substr($lin,15,strlen($lin)-15));
                                    }
                                    break;
                    }

                }

            $post_data["billtoaddress2"] = "";
            $post_data["billtostate"] = "";
            $post_data["shiptoaddress2"] = "";
            $post_data["shiptostate"] = "";
            $post_data["shiptotelephone"] = "";
            $post_data["promocode"] = "";

            $this->writeLogfile("************************************************");
            $this->writeLogfile('Processing Cart '.$post_data["webconfirmationno"]);
            $this->writeLogfile("************************************************");

            $post_data["TX"] = "HEADER";
            $return = $this->postData($post_data);
            if (!$return){
                $log = new phpbmsLog("Problem posting cart header.", "EMAIL CONN");
                $this->writeLogfile("Problem posting cart header:");
                $this->writeLogfile($return);
                return false;
            }

            $activewmsid = $return;
            $myline= 1;
            $myquantity = 0;
            $mytotalweight = 0;
            $mytotalcost = 0;
            $mytotalti = 0;

            foreach($orderlines as $i){
		$post_product = array();
		$post_product["TX"] = "DETAIL";

		$post_product["orderid"] = $activewmsid;

                $lin =  substr(strstr($i, ':'),1); //discard first part of text
//              $post_product["quantity"] = floatval(strstr($lin, 'x', true));
                $post_product["quantity"] = floatval(substr($lin, 0, strpos($lin, 'x')));
                $lin = substr(strstr($lin, 'x'),1);
//		$post_product["upc"] = trim(strstr($lin, ' ', true));
		$post_product["upc"] = trim(substr($lin, 0, strpos($lin, ' ')));
                $lin = substr(strstr($lin, ' '),1);
//              $post_product["brand"] = trim(strstr($lin, ':', true));
                $post_product["brand"] = trim(substr($lin, 0, strpos($lin, ':')));
                $lin = substr(strstr($lin, ':'),1);
//              $post_product["stylename"] = trim(strstr($lin, ',', true));
                $post_product["stylename"] = trim(substr($lin, 0, strpos($lin, ',')));
                $lin = substr(strstr($lin, ','),1);
//		$post_product["colour"] = trim(strstr($lin, '(', true));
		$post_product["colour"] = trim(substr($lin, 0, strpos($lin, '(')));
                $lin = str_replace("))", ")", substr(strstr($lin, '('),1));
//		$post_product["size"] = trim(strstr($lin, '-', true));
		$post_product["size"] = trim(substr($lin, 0, strpos($lin, ')')));
                if(strpos($post_product["size"], '(')>0)
                    $post_product["size"] = $post_product["size"].')';
//		$post_product["size"] = substr($post_product["size"], 0, -1);
                $lin = substr(strstr($lin, ')'),5);
//                $lin = substr(strstr($lin, '£'),1);
//              $post_product["unitprice"] = floatval(strstr($lin, '-', true));
                $post_product["unitprice"] = floatval(substr($lin, 0, strpos($lin, '-')));
                $post_product["unitprice"] = ($post_product["unitprice"]/$post_product["quantity"]);

                $post_product["unitcost"] = 0;
                $post_product["unitpromocode"] = 0;
                $post_product["unitdiscount"] = 0;
                $post_product["unitweight"] = 0.2;
		$post_product["currency"] = "GBP";

                $return = $this->postData($post_product);
                if (!$return){
                        $log = new phpbmsLog("Problem posting detail line.", "EMAIL CONN");
                        $this->writeLogfile("Problem posting detail line.");
                        $this->writeLogfile($return);
                        return false;
                }

                $myline ++;
                $myquantity += $post_product['quantity'];
                $mytotalweight += ($post_product['quantity'] * $post_product["unitweight"]);
                $mytotalcost += ($post_product['quantity'] * $post_product["unitcost"]);
                $mytotalti += ($post_product['quantity'] * $post_product["unitprice"]);

            }

            $log = new phpbmsLog("Problem posting detail line.", "EMAIL CONN");
            $this->writeLogfile("Processing cart totals...");

            //Confirm that what has been posted matches the totals
            $post_data = array();
            $post_data["TX"] = "TOTALS";
            $post_data["orderid"] = $activewmsid;
            $post_data["lines"] = intval($myline - 1);
            $post_data["quantity"] = $myquantity;
            $post_data["totalweight"] = $mytotalweight;
            $post_data["totalcost"] = $mytotalcost;
            $post_data["totaldiscount"] = 0;
            $post_data["totalti"] = $mytotalti;
//	      $post_data['total_discounts'] = floatval($cart->getDiscounts());

//            $writeLogfile("Cart ".$cartid." amount ".round(($totalti + $shipping), 2));

            $return = $this->postData($post_data);
            if (!$return){
                    $log = new phpbmsLog("Problem posting cart totals.", "EMAIL CONN");
                    $this->writeLogfile("Problem posting cart totals.");
                    $this->writeLogfile($return);
                return false;
            }

//echo "payment=".$variables["paymentmethod"].":".$variables["paymentreference"]."!<br>";
//echo "payment result=".$variables["paymentresult"]."!<br>";
//echo "payment currency=".$variables["paymentcurrency"]."!<br>";
//echo "payment total=".$variables["paymenttotal"]."!<br>";
//echo "payment auth=".$variables["paymentauthorisation"]."!<br>";
//echo "fraud check result=".$variables["fraudchecks"]."!<br>";
//echo "fraud address check=".$variables["fraudcheckaddress"]."!<br>";
//echo "fraud postcode check=".$variables["fraudcheckpostcode"]."!<br>";
//echo "fraud cv2 check=".$variables["fraudcheckcv2"]."!<br>";

            if (!imap_delete($myConn,$msg["msgno"])){
                $log = new phpbmsLog("Unable to delete message!", "EMAIL CONN");
                $this->writeLogfile("Unable to delete message!");
                exit;
            }

            $m++;
//            if ($m>10){
//                $log = new phpbmsLog("number of messages greater than throttle!", "EMAIL CONN");
//                $this->writeLogfile("number of messages greater than throttle!");
//                exit;
//            }
        }
        
        imap_expunge($myConn);

    }

    function writeLogfile($message){

        $filename = dirname(__FILE__)."/log/logs.txt";
        $message = date("d/m/Y H:i:s",time()).": ".$message."\n";

        // Let's make sure the file exists and is writable first.
        if (is_writable($filename)) {
            if (!$handle = fopen($filename, 'a')) {
                 $log = new phpbmsLog("Cannot open file ($filename)", "EMAIL CONN");
                 exit;
            }

            // Write $message to our opened file.
            if (fwrite($handle, $message) === FALSE) {
                $log = new phpbmsLog("Cannot write to file ($filename)", "EMAIL CONN");
                exit;
            }

            fclose($handle);

        } else {
            $log = new phpbmsLog("The file ($filename) is not writable", "EMAIL CONN");
        }
    }

}//end class --netro_emails--

$clean = new netro_emails();
$clean->processEmails();

?>
