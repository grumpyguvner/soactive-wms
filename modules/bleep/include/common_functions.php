<?PHP

/*
   Author:    Mark Horton (mark@hortonconsulting.co.uk)
    
    Important:
           These are common functions to be included WITHIN each bleep import class
           they basically provide common functionality across classes
                
*/

    function getCategoryID($bleepid){
	//
	//find and return the existing department category uuid (if one exists)
	//
	$mycategoryid = "";

	$querystatement="SELECT categoryid FROM departments WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve departments");
		$mycategoryid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mycategoryid = $therecord["categoryid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a categoryid
	//

        return $mycategoryid;

    }//end function getCategoryID

    function getColourID($bleepid){
	//
	//find and return the existing colour uuid (if one exists)
	//
	$mycolourid = "";

	$querystatement="SELECT uuid FROM colours WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve colours");
		$mycolourid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mycolourid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a colourid
	//

        return $mycolourid;

    }//end function getColourID

    function getLocationID($bleepid){
	//
	//find and return the existing location uuid (if one exists)
	//
	$mylocationid = "";

	$querystatement="SELECT uuid FROM locations WHERE bleepid='".$bleepid."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve locations");
		$mylocationid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mylocationid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a locationid
	//

        return $mylocationid;

    }//end function getLocationID


    function getProductID($bleepid){
	//
	//find and return the existing product uuid (if one exists)
	//
	$myproductid = "";

	$querystatement="SELECT uuid FROM products WHERE bleepid=".$bleepid;
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve products");
		$myproductid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$myproductid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a productid
	//

        return $myproductid;

    }//end function getProductID

    function getSizeID($bleepid){
	//
	//find and return the existing size uuid (if one exists)
	//
	$mysizeid = "";

	$querystatement="SELECT uuid FROM sizes WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve sizes");
		$mysizeid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mysizeid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a sizeid
	//

        return $mysizeid;

    }//end function getSizeID

    function getStyleID($bleepid){
	//
	//find and return the existing style uuid (if one exists)
	//
	$mystyleid = "";

	$querystatement="SELECT uuid FROM styles WHERE stylenumber='".($bleepid)."'";
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve styles");
		$mystyleid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mystyleid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a styleid
	//

        return $mystyleid;

    }//end function getStyleID

    function getSupplierID($bleepid){
	//
	//find and return the existing supplier uuid (if one exists)
	//
	$mysupplierid = "";

	$querystatement="SELECT uuid FROM suppliers WHERE bleepid=".((int) $bleepid);
	$queryresult = $this->db->query($querystatement);

	if(!$queryresult){
		$error= new appError(-310,"Error 310.","Could Not Retrieve Suppliers");
		$mysupplierid = false;

	} else {
		while($therecord=$this->db->fetchArray($queryresult)){

			$mysupplierid = $therecord["uuid"];

		}//end while

	}//endif queryresult
	//
	//we should now have a supplierid
	//

        return $mysupplierid;

    }//end function getSupplierID

?>