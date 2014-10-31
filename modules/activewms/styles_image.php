<?php 
$loginNoKick=true;
$loginNoDisplayError=true;
require_once("../../include/session.php");
session_cache_limiter('nocache');

if(isset($_GET["id"])) {
    $querystatement="SELECT image AS file, image_type AS type, image_name AS name, '' AS roleid FROM styles_images WHERE id=".((integer)$_GET["id"]);
    @ $queryresult=$db->query($querystatement);
    if($queryresult) {
            if($db->numRows($queryresult)){
                $therecord=$db->fetchArray($queryresult);

                $orig = imagecreatefromstring($therecord["file"]);
                //need to write the image to disk in order to work out the image dimensions!
                $filename='/tmp/temp';
                $fp = fopen($filename, 'w');
                fwrite($fp, $therecord["file"]);
                fclose($fp);
                list($width, $height) = getimagesize($filename);

                if(isset($_GET["size"])) {
                    $new_width = intval($_GET["size"]);
                    $percent = 1 / ($width / $new_width);

                    $new_height = $height * $percent;
                    $im = imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($im, $orig, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                }else{
                    $im = $orig;
                }

                header("Content-type: ".$therecord["type"]);
                if ($im !== false) {
                    switch ($therecord["type"]) {
                        case "image/gif":
                            imagegif($im);
                            imagedestroy($im);
                            break;
                        case "image/jpeg":
                            imagejpeg($im);
                            imagedestroy($im);
                            break;
                        case "image/png":
                            imagepng($im);
                            imagedestroy($im);
                            break;
                        default:
                            echo 'The image if not a supported type (must be gif, jpg or png).';
                            break;
                    }
                }else{
                    echo 'An error occurred.';
                }
            }
        }
}
?>