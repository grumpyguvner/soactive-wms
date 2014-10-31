<?php

    include("../../include/session.php");
    include("include/tables.php");
    include("include/fields.php");
    include("include/stylecategories.php");

    $thetable = new stylecategories($db, "tbld:3342a3d4-c6a2-3a38-6576-419299859561");
    $therecord = $thetable->processAddEditPage();

    if(isset($therecord["phpbmsStatus"]))
        $statusmessage = $therecord["phpbmsStatus"];

    $pageTitle="Style Category";

    $phpbms->cssIncludes[] = "pages/stylecategories.css";

        //Form Elements
        //==============================================================
        $theform = new phpbmsForm();

        $theinput = new inputCheckbox("inactive",$therecord["inactive"]);
        $theform->addField($theinput);

        $theinput = new inputField("name",$therecord["name"],NULL,true,NULL,32,128);
        $theform->addField($theinput);

        $theinput = new inputField("displayorder",$therecord["displayorder"],"display order",true,NULL,10,10);
        $theform->addField($theinput);

        $theinput = new inputField("webdisplayname",$therecord["webdisplayname"],"web display name");
        $theform->addField($theinput);

        $theinput = new inputTextarea("description", $therecord["description"]);
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


    <fieldset id="fsAttributes">
            <legend>attributes</legend>

            <p><?php $theform->showField("inactive")?></p>

            <p><?php $thetable->showParentsSelect($therecord["uuid"], $therecord["parentid"]); ?></p>

<!--            <p><?php // $thetable->showAttractiveSelect($therecord["attractiveid"]); ?></p> -->

            <p><?php $theform->showField("displayorder")?></p>

    </fieldset>

    <div id="leftDiv">

        <fieldset>
            <legend>name</legend>

            <p class="big"><?php $theform->showField("name")?></p>

        </fieldset>

        <fieldset>
            <legend>web</legend>

            <p><?php $theform->showField("webenabled")?></p>

            <p><?php $theform->showField("webdisplayname")?></p>

            <p><?php $theform->showField("description")?></p>

        </fieldset>

        <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

    </div>

    <?php
            $theform->showGeneralInfo($phpbms,$therecord);
            $theform->endForm();
    ?>
</div>
<?php include("footer.php");?>
