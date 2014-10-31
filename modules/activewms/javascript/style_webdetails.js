var style = {

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

    //toggles the web description between edit mode and previewing
    editPreviewWebDesc: function(){

        var editDiv = getObjectFromID("webDescEdit");
        var previewDiv = getObjectFromID("webDescPreview");
        var webDesc = getObjectFromID("webdescription");
        var thebutton = getObjectFromID("buttonWebPreview");

        if (thebutton.innerHTML == "preview"){

            thebutton.innerHTML = "edit";
            previewDiv.style.display = "block";
            editDiv.style.display = "none";
            previewDiv.innerHTML = webDesc.value;

        } else {

            thebutton.innerHTML = "preview";
            previewDiv.style.display = "none";
            editDiv.style.display = "block";

        }//endif

    },//end function editPreviewWebDesc


    // generic function setting hidden value associated with a picture
    // to a precise status
    _updatePicStatus: function(picture, status){

	var thechange=getObjectFromID(picture+"change");
	thechange.value=status;

    },//end function _updatePicStatus


    updateThumb: function(){

        style._updatePicStatus("thumb","upload");

    },//end function updateThumb


    updatePic: function(){

        style._updatePicStatus("picture","upload");

    },//end function updatePic


    _deletePicture: function (picture){

	var thepic = getObjectFromID(pic+"pic");
	var deleteDiv = getObjectFromID(pic+"delete");
	var addDiv = getObjectFromID(pic+"add");
	thepic.style.display = "none";
	deleteDiv.style.display = "none";
	addDiv.style.display = "block";

        style._updatePicStatus(pic,"delete");

    },//end function _deletePicture


    deleteThumb: function(){

        style._deletePicture("thumb");

    },//end function deleteThumb


    deletePic: function(){

        style._deletePicture("picture");

    },//end function deletePic


}//end class styles



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var theButton = getObjectFromID("buttonWebPreview");
    connect(theButton, "onclick", style.editPreviewWebDesc);

    var delThumbButton = getObjectFromID("deleteThumbButton");
    connect(delThumbButton, "onclick", style.deleteThumb);

    var thumbnailupload = getObjectFromID("thumbnailupload");
    connect(thumbnailupload, "onchange", style.updateThumb);

    var delPicButton = getObjectFromID("deletePictureButton");
    connect(delPicButton, "onclick", style.deletePic);

    var pictureupload = getObjectFromID("pictureupload");
    connect(pictureupload, "onchange", style.updatePic);

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", style.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", style.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", style.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", style.cancelForm);

    //set the initial focus
    var stylename = getObjectFromID("webenabled");
        stylename.focus();

})
