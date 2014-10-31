<?php

	class styleImage {

		var $styleuuid;
		var $styleid;

		function styleImage($db, $styleid){

			$this->db = $db;

			$this->styleid = (int) $styleid;

			$querystatement = "
				SELECT
					`uuid`
				FROM
					`styles`
				WHERE
					`id` = '".$this->styleid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);
				$this->styleuuid = $therecord["uuid"];
			}else{
				$this->styleuuid = "";
			}

		}//end method


		function getPageTitle(){

			//set the page title (need to grab style information)
			$querystatement = "
				SELECT
					stylenumber,
					stylename
				FROM
					styles
				WHERE
					id=".$this->styleid;

			$queryresult = $this->db->query($querystatement);
			$refrecord = $this->db->fetchArray($queryresult);

			$pageTitle = "Images: ";

			$pageTitle.=$refrecord["stylenumber"]." ".$refrecord["stylename"];

			$pageTitle = htmlQuotes($pageTitle);

			return $pageTitle;

		}//end method

	}//end class


?>