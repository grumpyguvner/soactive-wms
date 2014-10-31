<?php

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/sports.php");

    $thetable = new sports($db, "tbld:3492da16-fe26-11e0-a58e-0017083b723b");
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="Sport";

    $phpbms->cssIncludes[] = "pages/activewms/sports.css";
    $phpbms->jsIncludes[] = "modules/activewms/javascript/sport.js";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();
        $theform->enctype = "multipart/form-data";

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
        $theform->addField($theinput);

        $theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
        $theform->addField($theinput);

        $theinput = new inputField("webdisplayname",$therecord["webdisplayname"],"web display name");
        $theform->addField($theinput);

        $theinput = new inputTextarea("webdescription",$therecord["webdescription"],"webdescription", false, 4, 48, false);
        $theform->addField($theinput);

        $theinput = new inputField("weburl",$therecord["weburl"],"web url", true, NULL, 64, 32, false);
        $theform->addField($theinput);

        $theinput = new inputField("meta_title",$therecord["meta_title"],"meta title", true, NULL, 64, 128);
        $theform->addField($theinput);

        $theinput = new inputTextarea("meta_description",$therecord["meta_description"],"meta description", false, 4, 48);
        $theform->addField($theinput);

        $theinput = new inputField("meta_keywords",$therecord["meta_keywords"],"meta keywords", true, NULL, 64, 128);
        $theform->addField($theinput);

        $theinput = new inputDatePicker("displayfrom", $therecord["displayfrom"], "display from");
        $theform->addField($theinput);

        $theinput = new inputDatePicker("displayuntil", $therecord["displayuntil"], "display until");
        $theform->addField($theinput);

        $theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
        $theform->addField($theinput);

        if(isset($_GET["tabledefid"]) && !isset($therecord["id"])){

                $theinput = new inputSmartSearch($db, "fileid", "Pick File", "", "exisiting file", false, 40);
                $theform->addField($theinput);

        }//end if

        $theinput = new inputField("banner_image_name",$therecord["banner_image_name"],"file name",true,NULL,32,128);
        $theinput->setAttribute("class","important");
        $theform->addField($theinput);

        $theinput = new inputField("banner_image_type", $therecord["banner_image_type"], "file type", false, null, 25);
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
                <img id="thumbpic" src="/modules/activewms/sports_image.php?id=<?php echo $therecord["id"]?>&size=153" alt="<?php echo $therecord["banner_image_name"] ?>" style="border: 1px solid black; display: block; margin: 3px;;" />
                <?php if(isset($therecord["banner_image_name"]) && $therecord["banner_image_name"]!="") {?>
                <p>
                        <button  type="button" class="Buttons" onclick="document.location='<?php echo $therecord["apifileurl"]?>&target=_blank'">View/Download <?php echo $therecord["banner_image_name"] ?></button>
                </p>
                <p>
                        <?php $theform->showField("banner_image_name")?><br />
                        <span class="notes">If the file name does <strong>not</strong> include an extension your browser may not be able to download/view the file correctly.</span>
                </p>
                <p><?php $theform->showField("banner_image_type"); ?></p>
                <?php }?>
                <p>
                        <label for="upload_banner_image">replace file</label><br />
                        <input id="upload_banner_image" name="upload_banner_image" type="file" accept="image/gif, image/jpeg, image/png" size="64" tabindex="260" />
                </p>
        </fieldset>

        <fieldset id="fsAttributes">
                <legend>attributes</legend>

                <p><?php $theform->showField("webenabled")?></p>

                <p><?php $theform->showField("inactive")?></p>

                <p><?php $thetable->showParentsSelect($therecord["uuid"], $therecord["parentid"]); ?></p>

    <!--            <p><?php // $thetable->showAttractiveSelect($therecord["attractiveid"]); ?></p> -->

                <p><?php $theform->showField("displayorder")?></p>

        </fieldset>
    </div>

    <div id="leftDiv">

        <fieldset>
            <legend>identification</legend>

            <p class="big"><?php $theform->showField("name")?></p>

        </fieldset>

        <fieldset>
            <legend>web</legend>

            <p><?php $theform->showField("webdisplayname")?></p>

            <label for="webdescription">web description <span class="notes">(HTML acceptable)</span></label><br />
            <div style=" <?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
                    <textarea id="webdescription" name="webdescription" cols="60" rows="6"><?php echo $therecord["webdescription"] ?></textarea>
            </div>
            <div style=" <?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
            <?php echo $therecord["webdescription"] ?>
            </div>
            <div><button id="buttonWebPreview" type="button" class="Buttons"><?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?></button></div>

            <label for="weburl">friendly url <span class="notes">(lowercase letters, numbers and hyphens only!)</span></label><br />
            <p><?php $theform->showField("weburl")?></p>

            <p><?php $theform->showField("meta_title")?></p>
            <p><?php $theform->showField("meta_description")?></p>
            <p><?php $theform->showField("meta_keywords")?></p>

            <p><?php $theform->showField("displayfrom")?></p>
            <p><?php $theform->showField("displayuntil")?></p>

        </fieldset>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
