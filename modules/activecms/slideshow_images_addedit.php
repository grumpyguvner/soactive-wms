<?php

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/slideshow_images.php");

    $thetable = new slideshow_images($db, "");
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="Style Image";

    $phpbms->cssIncludes[] = "pages/activewms/slideshow_images.css";
    $phpbms->jsIncludes[] = "modules/activewms/javascript/slideshow_image.js";

    //Form Elements
    //==============================================================
    $theform = new phpbmsForm();
    $theform->enctype = "multipart/form-data";

    $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
    $theform->addField($theinput);

    $theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
    $theform->addField($theinput);

    $theinput = new inputField("alt_text",$therecord["alt_text"],NULL,true,NULL,32,128);
    $theform->addField($theinput);

    $theinput = new inputDataTableList($db, "styleid",$therecord["styleid"],"styles","uuid","CONCAT(stylenumber,' ',stylename)",
                                                    "inactive=0", "stylenumber", true, "style", true, "");
    $theform->addField($theinput);

    $theinput = new inputDataTableList($db, "colourid",$therecord["colourid"],"colours","uuid","CONCAT(LPAD(bleepid,4,'0'),' ',name)",
                                                    "inactive=0", "bleepid", true, "colour", true, "");
    $theform->addField($theinput);

    $theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
    $theform->addField($theinput);

    $theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
    $theform->addField($theinput);

    $theinput = new inputField("image_name",$therecord["image_name"],"file name",true,NULL,32,128);
    $theinput->setAttribute("class","important");
    $theform->addField($theinput);

    $theinput = new inputField("image_type", $therecord["image_type"], "file type", false, null, 25);
    $theinput->setAttribute("readonly", "readonly");
    $theinput->setAttribute("class", "uneditable");
    $theform->addField($theinput);

    $thetable->getCustomFieldInfo();
    $theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
    $theform->jsMerge();
    //==============================================================
    //End Form Elements

    include("header.php");

?><div class="bodyline">
    <?php $theform->startForm($pageTitle);?>

    <div id="rightsideDiv">
        <fieldset>
                <legend>image</legend>
                <img id="thumbpic" src="/modules/activewms/slideshow_image.php?id=<?php echo $therecord["id"]?>&size=153" alt="<?php echo $therecord["image_name"] ?>" style="border: 1px solid black; display: block; margin: 3px;;" />
                <?php if(isset($therecord["image_name"]) && $therecord["image_name"]!="") {?>
                <p>
                        <button  type="button" class="Buttons" onclick="document.location='<?php echo $therecord["apifileurl"]?>&target=_blank'">View/Download <?php echo $therecord["image_name"] ?></button>
                </p>
                <p>
                        <?php $theform->showField("image_name")?><br />
                        <span class="notes">If the file name does <strong>not</strong> include an extension your browser may not be able to download/view the file correctly.</span>
                </p>
                <p><?php $theform->showField("image_type"); ?></p>
                <?php }?>
                <p>
                        <label for="upload_image">replace file</label><br />
                        <input id="upload_image" name="upload_image" type="file" accept="image/gif, image/jpeg, image/png" size="64" tabindex="260" />
                </p>
        </fieldset>

        <fieldset id="fsAttributes">
                <legend>attributes</legend>

                <p><?php $theform->showField("webenabled")?></p>

                <p><?php $theform->showField("inactive")?></p>

                <p><?php $theform->showField("displayorder")?></p>

        </fieldset>
    </div>

    <div id="leftDiv">

        <fieldset>
            <legend>identification</legend>

            <p class="big"><?php $theform->showField("name")?></p>

            <p><?php $theform->showField("alt_text")?></p>

            <p><?php $theform->showField("styleid") ?></p>

            <p><?php $theform->showField("colourid") ?></p>
            
        </fieldset>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
