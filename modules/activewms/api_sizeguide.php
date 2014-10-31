<?php
define("APP_DEBUG",true);
define("noStartup",true);

require("../../include/session.php");
require("../../include/common_functions.php");

class returnSizeGuide {

	function process($reference){

		$this->phpbmsSession = new phpbmsSession;

		if($this->phpbmsSession->loadDBSettings(false)){

			@ include_once("include/db.php");

			$this->db = new db(false);
			$this->db->stopOnError = false;
			$this->db->showError = false;
			$this->db->logError = false;

		} else {

			echo $this->returnJSON(false, "Could not open session.php file");
                        exit;

		}

                $stylenumber = explode('-', $reference);
                $querystatement = "
                        SELECT
                                        IFNULL(sg.webdescription,'There is currently no size information for this product.') AS HTML
                        FROM
                                        styles s LEFT JOIN sizeguides sg ON (s.sizeguideid = sg.uuid)

                        WHERE
                                        s.stylenumber = '".$stylenumber[0]."'";

                $queryresult = $this->db->query($querystatement);

                $therecord = $this->db->fetchArray($queryresult);

                echo $therecord["HTML"];

	}//endfunction process

        function returnJSON($success, $details, $extras = null){

                $thereturn["success"] = $success;
                $thereturn["details"] = $details;

                if($extras)
                        $thereturn["extras"] = $extras;

                return json_encode($thereturn);

        }//endfunction returnJSON

}//end class returnSizeGuide

// START PROCESSING
//==============================================================================
header('Access-Control-Allow-Origin: *');

$returnSizeGuide = new returnSizeGuide();

if(!isset($_GET["ref"])){
    echo $returnSizeGuide->returnJSON("failure", "No reference given.");
}else{
    $returnSizeGuide->process($_GET["ref"]);
}