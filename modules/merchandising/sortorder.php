<?php
    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("../activewms/include/styles.php");

    if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$pageTitle="Merchandising";

	$phpbms->cssIncludes[] = "jqueryui/jquery-ui-1.8.20.redmond.css";
//	$phpbms->cssIncludes[] = "jqueryui/jquery-ui-1.8.20.cupertino.css";
        
	$phpbms->jsIncludes[] = "modules/merchandising/javascript/jquery-1.7.2.min.js";
	$phpbms->jsIncludes[] = "modules/merchandising/javascript/jquery-ui-1.8.20.custom.min.js";
	$phpbms->jsIncludes[] = "modules/merchandising/javascript/sortorder.js";

	$thetable = new styles($db,"tbld:7ecb8e4e-8301-11df-b557-00238b586e42", $backurl);
        $therecord = $thetable->getRecord('styl:1f8f6817-c8c2-5bb2-59c9-3da4edddccbe', true);

	include("header.php");
?>

<div id="fruit_sort">
	<div id="fruit_1">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1305" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_2">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1304" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_4">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1303" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_5">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1302" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_6">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1301" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_7">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1300" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
	<div id="fruit_8">
            <img id="thumbpic" src="/modules/activewms/styles_image.php?id=<?php echo "1299" ?>&size=153" style="width: 153px; height: 205px; border: 1px solid black; display: block; margin: 3px;;" />
        </div>
</div>

<?php include("footer.php");?>