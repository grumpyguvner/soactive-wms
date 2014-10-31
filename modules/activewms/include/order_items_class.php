<?php

	class orderItems {

		var $orderuuid;
		var $orderid;

		function orderItems($db, $orderid){

			$this->db = $db;

			$this->orderid = (int) $orderid;

			$querystatement = "
				SELECT
					`uuid`
				FROM
					`orders`
				WHERE
					`id` = '".$this->orderid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){
				$therecord = $this->db->fetchArray($queryresult);
				$this->orderuuid = $therecord["uuid"];
			}else{
				$this->orderuuid = "";
			}

		}//end method


		function getPageTitle(){

			//set the page title (need to grab order information)
			$querystatement = "
				SELECT
					webconfirmationno,
					billtoname
				FROM
					orders
				WHERE
					id=".$this->orderid;

			$queryresult = $this->db->query($querystatement);
			$refrecord = $this->db->fetchArray($queryresult);

			$pageTitle = "Order Items: ";

			$pageTitle.=$refrecord["webconfirmationno"]." ".$refrecord["billtoname"];

			$pageTitle = htmlQuotes($pageTitle);

			return $pageTitle;

		}//end method

	}//end class


?>