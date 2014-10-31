<?php

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/sizeguides.php");

    $thetable = new sizeguides($db, "tbld:b95fea3c-594c-11e1-98b4-2bfbd1d96b8");
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="Size Guide";

    $phpbms->cssIncludes[] = "pages/activewms/sizeguides.css";
    $phpbms->jsIncludes[] = "modules/activewms/javascript/sizeguides.js";

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

        $theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
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

        </fieldset>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
