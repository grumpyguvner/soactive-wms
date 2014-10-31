
var sport = {

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

}//end class sport



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var theButton = getObjectFromID("buttonWebPreview");
    connect(theButton, "onclick", sport.editPreviewWebDesc);

    //set the initial focus
    var sportname = getObjectFromID("name");
        sportname.focus();

})
