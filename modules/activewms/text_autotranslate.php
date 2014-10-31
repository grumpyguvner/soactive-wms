<?php

	//parse any command line arguments into the $_GET variable
	foreach ($argv as $arg) {
		if (ereg('([^=]+)=(.*)',$arg,$reg)) {
			$_GET[$reg[1]] = $reg[2];
		} elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)) {
			$_GET[$reg[1]] = 'true';
		}
	} 

	include("include/google_translate.php");

	if(!isset($_GET["text"])){
		echo "Nothing to translate\n";
		return false;
	}

	$iso_code="fr";

	$translate = new GoogleTranslateApi();
	$translate->ToLang = $iso_code;
	$langText = $_GET["text"];
	$translate->Text = $langText;
	if($langText){
		$langText = $translate->translate();
		if(!$langText){
			$langText = $_GET["text"];
			echo "error translating text ->".$langText."\n";
			echo "Google response ->".$translate->DebugMsg."\n";
			return 99;
		}
	}

	echo $langText;
	return 0;

?>
