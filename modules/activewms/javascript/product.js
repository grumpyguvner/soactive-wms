
var product = {

    submitForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");

        if(!validateForm(theform))
            return false;

        hiddenCommand.value = "save";

        theform.submit();

        return true;

    },//end function submitForm


    cancelForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");
        var cancelclick= getObjectFromID("cancelclick");

        cancelclick.value = true;
        hiddenCommand.value ="cancel";

        theform.submit();

        return true;

    },//end function cancelForm


    checkbleepid: function(){

        var bleepid = getObjectFromID("bleepid");
        var excludeid = getObjectFromID("uuid");

        if(!checkUnique('tbld:7ecb8e4e-8301-11df-b557-00238b586e42', "bleepid", bleepid.value, excludeid.value)){

            alert("Bleep id number must be unique.");
            bleepid.value="";
            bleepid.focus();

        }//endif

    },//end function checkbleepid


}//end class product



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var bleepid = getObjectFromID("bleepid");
    connect(bleepid, "onchange", product.checkbleepid);

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", product.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", product.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", product.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", product.cancelForm);

    //set the initial focus
//    var stylename = getObjectFromID("stylename");
//        stylename.focus();

})
