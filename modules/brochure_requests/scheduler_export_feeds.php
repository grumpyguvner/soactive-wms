<?php
set_time_limit(0);

//uncomment for debug purposes
//if(!class_exists("appError"))
//	include_once("../../include/session.php");

include_once("../../include/search_class.php");

class feedDefinition extends displayTable{

    var $feedoptions;

    function initialize($data){

            $this->feedoptions = $data;

            parent::initialize($this->feedoptions['tabledefid']);

    }

    function exportFeed(){

            $this->issueQuery();
            $this->exportResults();

    }

    function issueQuery(){
            $querycolumns="";
            $tempSortOrder = "";

            //GROUPING SETUP
            if($this->showGroupings){
                    $i =1 ;
                    foreach ($this->thegroupings as $thegroup){
                            $querycolumns .= ", ".$thegroup["field"]." as \"_group".$i."\" ";
                            $tempSortOrder .= ", ".$thegroup["field"];
                            if($thegroup["ascending"] == 0)
                                    $tempSortOrder.=" DESC";
                            $i++;
                    }
                    if($i > 1){
                            $tempSortOrder = substr($tempSortOrder,2).", ";
                    }
            }

            foreach ($this->thecolumns as $therow)
                    $querycolumns.=", ".$therow["column"]." as \"".$therow["name"]."\"";
            $querycolumns=substr($querycolumns,2);

            $tempSortOrder .= $this->thetabledef["defaultsortorder"];
            $this->therecords=$this->thetabledef["querytable"]." ".$this->queryjoinclause;
            if($this->thetabledef["defaultwhereclause"]!="")
                $this->therecords.=" WHERE ".$this->thetabledef["defaultwhereclause"];
            if($tempSortOrder!="")
                $this->therecords.=" ORDER BY ".$tempSortOrder;
            $this->querystatement = "SELECT DISTINCT ".$this->thetabledef["maintable"].".id as theid,".$querycolumns." FROM ".$this->therecords;

            //save the query for total and display purposes
            $_SESSION["thequerystatement"] = $this->querystatement;
            //Add limit (settings)
//            $_SESSION["thequerystatement"].=" limit ".$this->recordoffset.", ".RECORD_LIMIT.";";

            $this->db->logError=false;
            $this->db->stopOnError=false;

            $this->queryresult = $this->db->query($_SESSION["thequerystatement"]);
//$log = new phpbmsLog($_SESSION["thequerystatement"], "DEBUG");

            $this->db->logError=true;
            $this->db->stopOnError=true;

            if($this->queryresult){
                     $this->numrows=$this->db->numRows($this->queryresult);
//                     if($this->numrows==RECORD_LIMIT or $this->recordoffset!=0){
                        //if you max the record limit or are already offsetiing get the true count

//                            $truecountstatement = "
//                                    SELECT
//                                            count(distinct ".$this->thetabledef["maintable"].".id) as thecount
//                                            ".getSearchFrom($this->querystatement);
//                            $truequeryresult=$this->db->query($truecountstatement);

//                            $truerecord=$this->db->fetchArray($truequeryresult);
//                            $this->truecount=$truerecord["thecount"];
//                     }
//                     else
                         $this->truecount=$this->numrows;
                    $this->sqlerror="";
            }else{
                    $this->sqlerror=$this->db->error;
                    $this->numrows=0;
                    $this->truecount=0;
            }
            $_SESSION["sqlerror"]=$this->sqlerror;

    }//end function

    function exportResults(){

        $this->writeToFile('<?xml version="1.0"?>', true);
        $this->writeToFile('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">');
        $this->writeToFile('<channel>');
        $this->writeToFile('<title>'.$this->feedoptions['name'].'</title>');
        $this->writeToFile('<link>'.$this->feedoptions['name'].'</link>');
        $this->writeToFile('<description>'.$this->feedoptions['description'].'</description>');
        
        $rownum=1;
        $this->db->seek($this->queryresult,0);

        while($therecord = $this->db->fetchArray($this->queryresult)){

                $this->writeToFile('<item>');

                if ($rownum==1) $rownum++; else $rownum=1;

                foreach($this->thecolumns as $thecolumn){
                    $content="";
                    $content.="<".$thecolumn["name"].">";
                    $content.=formatVariable($therecord[$thecolumn["name"]],$thecolumn["format"]);
                    $content.="</".$thecolumn["name"].">";
                    //Only write non-blanks
                    if($therecord[$thecolumn["name"]]<>"")
                        $this->writeToFile($content);
                }

                $this->writeToFile('</item>');

        }//endwhile

        $this->writeToFile('</channel>');
        $this->writeToFile('</rss>');

    }//end method

    function writeToFile($somecontent,$initialise=false){

            $filename = dirname(__FILE__).'/feeds/'.$this->feedoptions['filename'];

            // Let's make sure the file exists and is writable first.
            if (is_writable($filename)) {

                // In our example we're opening $filename in append mode.
                // The file pointer is at the bottom of the file hence
                // that's where $somecontent will go when we fwrite() it.
                if($initialise)
                    $handle = fopen($filename, 'w');
                else
                    $handle = fopen($filename, 'a');

                if (!$handle) {
                     echo "Cannot open file ($filename)";
                     return false;
                }

                // Write $somecontent to our opened file.
                if (fwrite($handle, $somecontent."\n") === FALSE) {
                    echo "Cannot write to file ($filename)";
                    return false;
                }

                fclose($handle);

            } else {
                echo "The file $filename is not writable";
            }

            return true;

    }//end method

}

class exportFeeds{

	function exportFeeds($db){
		$this->db = $db;
	}//end method --exportFeeds--

	//This method should iterate through all active feeds and submit
	function beginProcess(){

		$querystatement = "
			SELECT
                              `uuid`
			FROM
				`product_feeds`
			WHERE
				`inactive` = 0
			ORDER BY
				`priority`, `name`
			;";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			while($therecord = $this->db->fetchArray($queryresult)){
                            $this->processFeed($therecord['uuid']);
                        }

		}//end if

	}//end method --beginProcess-

	//This method will generate the file for a specific feed
	function processFeed($uuid){

		$querystatement = "
			SELECT
                              `name`,
                              `tabledefid`,
                              `site`,
                              `filename`,
                              `fileformat`,
                              `uploadurl`,
                              `username`,
                              `password`
			FROM
				`product_feeds`
			WHERE
				`uuid`='$uuid';
			";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			$therecord = $this->db->fetchArray($queryresult);

                        $displayTable= new feedDefinition($this->db);

                        $displayTable->initialize($therecord);
                        $displayTable->exportFeed();

		}//end if

	}//end method --beginProcess-

}//end class --cleanImports--

if(!isset($noOutput) && isset($db)){

    $clean = new exportFeeds($db);
    $clean->beginProcess();

}//end if
?>
