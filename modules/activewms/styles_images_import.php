<?php
$loginNoKick=true;
$loginNoDisplayError=true;
require_once("../../include/session.php");
/////////////////////////////////////////////
//   IMPORT STYLES FROM ACTIVEWMS          //
/////////////////////////////////////////////

class activewmsImages{

        private $styleno = "";
	private $limit = "";

	function activewmsImages($styleno="",$limit="10"){

		$this->styleno = $styleno;
		$this->limit = $limit;

	}//end method --activewmsImages--

	//This method should import new and updated records
	//from the uk warehouse database
	function beginImport(){

            $this->importAllImages();

	}//end method --beginImport--

        //This method should import new and updated records
	//from the bleep database
	function importAllImages(){

		// fetch all products from activeWMS
		$sqlQuery="SELECT DISTINCT styles.stylenumber, colours.bleepid
                                         FROM (products
                                         JOIN styles ON (products.styleid = styles.uuid)
                                         JOIN colours ON (products.colourid = colours.uuid))
                                         LEFT JOIN styles_image ON (styles.uuid = styles_images.styleid AND colours.uuid = styles_images.colourid)
                                         WHERE styles_images.styleid IS NULL";

		if($this->styleno==""){
			if(!$startref=="") $sqlQuery.=" AND (styles.stylenumber>".$startref.")";
                        if(!$this->limit=="") $sqlQuery.=" AND (styles.stylenumber < ".(int)($startref+$this->limit).")";
		}else{
			$sqlQuery.=" AND NOT (styles.stylenumber < ".$this->styleno.")";
                        if(!$this->limit=="") $sqlQuery.=" AND (styles.stylenumber < ".(int)($this->styleno+$this->limit).")";
		}
		$sqlQuery.=" ORDER BY styles.stylenumber DESC, colours.bleepid";
		$sqlQuery.=" LIMIT 50";

		$activewmsProducts = $db->query($sqlQuery);

		$activewmsProducts = explode(chr(10),$activewmsProducts);
		$recordsProcessed = 0;

		// We retreive all product IN ACTIVEWMS
		foreach ($activewmsProducts as $activewmsProduct){

			$lineProduct = explode(chr(9),$activewmsProduct);
			if (is_array($lineProduct) && isset($lineProduct[0]) && !empty($lineProduct[0])){
				$styleref = $lineProduct[0];
				$colourref = $lineProduct[1];

                                if ($this->importProduct($styleref,$colourref)){
                                    $recordsProcessed=$recordsProcessed+1;
                                }

			}//endif lineproduct

		}//end foreach

		echo $recordsProcessed." product records processed.\n";

	}//end method --importAllImages--

	//This method should import new and updated records
	//from the bleep database
	function importProduct($myStyle="",$myColour=""){

		// fetch all products from activeWMS
		$sqlQuery="SELECT DISTINCT(CONCAT(LPAD(styles.stylenumber,4,'0'), '-', LPAD(colours.bleepid,4,'0'))),
                                                styles.stylenumber,
                                                colours.bleepid
                                         FROM products
                                         JOIN styles ON (products.styleid = styles.uuid)
                                         JOIN colours ON (products.colourid = colours.uuid)
                                        WHERE styles.stylenumber='".$myStyle."'
                                              AND colours.bleepid =".$myColour.";";

		$activewmsProducts = $db->query($sqlQuery);

		$activewmsProducts = explode(chr(10),$activewmsProducts);
		$recordsProcessed = 0;

		// We retreive all product IN ACTIVEWMS
		foreach ($activewmsProducts as $activewmsProduct){

			$lineProduct = explode(chr(9),$activewmsProduct);
			if (is_array($lineProduct) && isset($lineProduct[0]) && !empty($lineProduct[0])){
                                $reference = $lineProduct[0];
				$styleref = $lineProduct[1];
				$colourref = $lineProduct[2];

echo "\n\n";
echo "<br/>************ IMPORTING IMAGES ************ \n";
echo '<br/>style --> '.$reference.'\n';

				$importStyle = new styles();
				$importStyle = $importStyle->getByReference(pSQL($styleref));
                                //only continue if product already exists
				if(isset($importStyle)){

                                        if (!$this->fetchImages($importStyle, $styleref, $colourref)){
                                            //If no images then try old school method before failing
                                            if (!$this->useOldImages($importStyle, $styleref, $colourref)){
                                                    echo "no images found ... \n";
                                            }
                                        }

                                }

			}//endif lineproduct

		}//end foreach

                return true;

	}//end method --importProducts--

	function fetchImages($styleObject, $myStyle="",$myColour=""){

		// fetch all image attributes (Size/Colour combos) from activeWMS
		$sqlQuery="SELECT DISTINCT i.id, i.image_name, i.alt_text
                                     FROM styles_images i
                                     JOIN styles s ON (i.styleid = s.uuid)
                                     JOIN colours c ON (i.colourid = c.uuid)
                                WHERE s.stylenumber = '".$myStyle."'
                                  AND c.bleepid = ".$myColour."
                             ORDER BY i.displayorder";

		$activewmsImages = $db->query($sqlQuery);

		$activewmsImages = explode(chr(10),$activewmsImages);
                $imgCount=0;

		// We retreive all images from ACTIVEWMS
		foreach ($activewmsImages as $activewmsImage){

			$productImages = explode(chr(9),$activewmsImage);
			if (is_array($productImages) && isset($productImages[0]) && !empty($productImages[0])){

                            $imageurl = '/modules/activewms/styles_image.php?id='.$productImages[0];
echo "<br/>image id --> ".$imageurl.'\n';
                            // Confirm product image is in ACTIVEWMS
                                $tempfile = '/tmp/IMG'.str_pad($myStyle, 4, '0', STR_PAD_LEFT).str_pad($myColour, 4, '0', STR_PAD_LEFT).'-'.$imgCount.'.jpg';
                                if (file_exists($tempfile)){
                                        echo '<br/>deleting '.$tempfile." so that we can recreate\n";
                                        @unlink($tempfile);
                                }

                                if(!$this->get_imagefile('http://activewms.sheactive.net'.$imageurl, $tempfile, true, $myStyle)){
                                        echo '<br/>could not fetch file from activewms\n';
                                }else{
                                        echo '<br/>Image valid.\n';
                                        $imgCount++;
                                }
//                            }//end foreach

                        }
                }

                return ($imgCount>0);

	}//end method --fetchImages--

	function useOldImages($styleObject, $myStyle="",$myColour=""){

		// fetch all product attributes (Size/Colour combos) from activeWMS
		$sqlQuery="SELECT DISTINCT IFNULL(CONCAT(styles.image_folder,styles.main_image),CONCAT('/images/items/historic/IMG',LPAD(styles.stylenumber,4,'0'),LPAD(colours.bleepid,4,'0'),'.jpg')) AS imageurl,
                                        IFNULL(CONCAT(styles.image_folder,styles.alt_image1),'') AS alt_image1,
                                        IFNULL(CONCAT(styles.image_folder,styles.alt_image2),'') AS alt_image2,
                                        IFNULL(CONCAT(styles.image_folder,styles.alt_image3),'') AS alt_image3,
                                        IFNULL(CONCAT(styles.image_folder,styles.alt_image4),'') AS alt_image4
                                     FROM products
                                     JOIN styles ON (products.styleid = styles.uuid)
                                     JOIN colours ON (products.colourid = colours.uuid)
                                WHERE styles.stylenumber = '".$myStyle."' AND colours.bleepid = '".$myColour."'
                             ORDER BY styles.stylenumber, colours.bleepid";

		$activewmsImages = $db->query($sqlQuery);

		$activewmsImages = explode(chr(10),$activewmsImages);
                $imgCount=0;

		// We retreive all images from ACTIVEWMS
		foreach ($activewmsImages as $activewmsImage){

			$productImages = explode(chr(9),$activewmsImage);
			if (is_array($productImages) && isset($productImages[0]) && !empty($productImages[0])){

                            // We retreive all product IN ACTIVEWMS
                            foreach ($productImages as $imageurl){
                                if ($imageurl!="" && substr($imageurl, -1)!="/"){
echo "<br/>image --> ".$imageurl.'\n';
                                    $imgCount++;
                                    //we only want to upload each image once
            //                        $tempfile = _PS_IMG_DIR_.'tmp/product_'.$styleObject['id.'.jpg';
                                    $tempfile = '/tmp/IMG'.str_pad($myStyle, 4, '0', STR_PAD_LEFT).str_pad($myColour, 4, '0', STR_PAD_LEFT).'-'.$imgCount.'.jpg';
                                    if (file_exists($tempfile)){
                                            echo '<br/>deleting '.$tempfile." so that we can recreate\n";
                                            @unlink($tempfile);
                                    }

                                    if(!$this->get_imagefile('http://activewms.sheactive.net'.$imageurl, $tempfile, true, $myStyle)){
                                            echo '<br/>could not fetch file from activewms\n';
                                    }else{
                                            echo '<br/>Uploading image to product.\n';
                                            $lastimageid = $this->uploadImage($styleObject, $tempfile, $imgCount);
                                    }
                                }
                            }//end foreach

                        }
                }

                return ($imgCount>0);

	}//end method --useOldImages--

	function get_imagefile($remote_path, $newfilename,$usenoimageurl=FALSE,$myStyle=""){

		$err_msg = '';
                $remote_path=trim($remote_path);
                $newfilename=trim($newfilename);
		$out = fopen($newfilename, 'wb');
		if ($out == FALSE){
			echo '<br/>Unable to create file '.$newfilename.'\n';
			return false;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_FILE, $out);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $remote_path);

		if (curl_exec($ch)===false){
			echo '<br/>curl error fetching file ('.$remote_path.'), response: '.curl_error($ch).'\n';
			fclose($out);
			@unlink($out);
			$out = false;
		} else {
			fclose($out);
		        $info = @curl_getinfo($ch);
			echo '<br/>http response: '.$info['http_code'].'\n';
			if (($info['http_code'] <> 200)){
				echo '<br/>http error fetching file ('.$remote_path.'), response: '.$info['http_code'].'\n';
				@unlink($out);
				$out = false;
			}
		}

		if(!$out){
			//only use the default image if $usenoimageurl set to true
			if(!$usenoimageurl) return false;

			echo '<br/>using image unavailable image\n';
			$url = Configuration::get('ACTIVEWMS_IMAGEUNAVAILABLEURL');

			if (!file_exists($url)){
				echo '<br/>unavailable file '.$url.' not found!\n';
				return false;
			}else{
				if (!copy ($url,$newfilename)){
					echo '<br/>error creating '.$newfilename.'\n';
					return false;
				}
			}
		}

		curl_close($ch);

		return file_exists($newfilename);

	}//end function --get_imagefile--

	function uploadImage($styleObject, $url, $position=0, $legend=""){

                $image = new Image();
		$image->id_product = $styleObject['id'];
                echo '<br/>product_id '.$image->id_product.' file '.$url.'\n';
		if($legend=="")
			$legend = $styleObject['name'];

		if($position==0) $position = 1;
echo '<br/>position='.$position.'\n';
		//if we haven't already found a cover image then set one now
//		if ($this->foundCover==false){
                if($position==1){
			$cover = 1;
//			$this->foundCover = true;
                        $legend=$styleObject['name'];
echo '<br/>Setting as cover image: '.$image->legend.'\n';
		}else{
			$cover = 0;
                        $legend=$styleObject['name']." alternate image ";
//                        $image->legend=$legend." image ".$position;
echo '<br/>Setting as alternate image '.$position.': '.$legend.'\n';
                }

echo '<br/>removing existing image ...\n';
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'image` WHERE `id_product`= '.$styleObject['id'].' AND `position`='.($position));

echo '<br/>Adding image ...\n';
echo 'INSERT INTO `'._DB_PREFIX_.'image` (`id_product`, `position`, `cover`) VALUES ('.$styleObject['id'].','.($position).','.intval($cover).')';
                Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'image` (`id_product`, `position`, `cover`) VALUES ('.$styleObject['id'].','.($position).','.intval($cover).')');
                $id_image = intval(Db::getInstance()->Insert_ID());
echo '<br/>Adding image caption ...\n';
echo 'INSERT INTO `'._DB_PREFIX_.'image_lang` (`id_image`, `id_lang`, `legend`) VALUES ('.($id_image).','.($defaultLanguageId).',\''.($legend).'\')';
                Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'image_lang` (`id_image`, `id_lang`, `legend`) VALUES ('.($id_image).','.($defaultLanguageId).',\''.($legend).'\')');

		if (isset($id_image)){
echo '<br/>Copying image to product folder and creating thumbnails \n';
			$this->copyImage($styleObject, $id_image, $url);
                }

//		if (!$image->update())
//			echo '<br/>error while updating image\n';
//		$id_image = $image->id;
//echo '<br/>Image id ='.$id_image.'\n';

		return $id_image;
	}//end method --uploadImage--

	/**
	 * Copy a product image
	 *
	 * @param integer $id_product Product Id for product image filename
	 * @param integer $id_image Image Id for product image filename
	 */
	function copyImage($styleObject, $id_image, $url)
	{
		require_once('../../images.inc.php');

                $image = new Image($id_image);
                if (!$new_path = $image->getPathForCreation())
                        echo '<br/>An error occurred during new folder creation\n';
//		$newfile = stripslashes($new_path.$styleObject['id'].'-'.$id_image.$image->image_format);
		$newfile = stripslashes($new_path.'.'.$image->image_format);

		if (!file_exists($url)){
			echo '<br/>temp file '.$url." not found!\n";
			return false;
		}

		if (file_exists($newfile)){
			echo '<br/>deleting '.$newfile." so that we can recreate\n";
			@unlink($newfile);
			return false;
		}

		if (!copy ($url,$newfile)){
			echo '<br/>error creating '.$newfile.'\n';
			@unlink($newfile);
			return false;
		}

		Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $styleObject['id']));

		$imagesTypes = ImageType::getImagesTypes('products');
		foreach ($imagesTypes AS $k => $imageType){
//			$thumbfile = stripslashes(_PS_IMG_DIR_."p/".$styleObject['id']."-".$id_image."-".$imageType['name'].".jpg");
			$thumbfile = stripslashes($new_path."-".$imageType['name'].".".$image->image_format);
			if (file_exists($thumbfile)){
				echo '<br/>deleting '.$thumbfile." so that we can recreate\n";
				@unlink($thumbfile);
			}
			if (!imageResize($url,$thumbfile, $imageType['width'], $imageType['height'], $image->image_format))
				echo '<br/>an error occurred while creating image '.stripslashes($imageType['name']).'\n';
		}

	}//end method --copyImage--

}
?>
