var style = {

    submitForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");

        if(!validateForm(theform))
            return false;

        var theprice = getObjectFromID("unitprice");
        var thesale = getObjectFromID("saleprice");
        unitprice = currencyToNumber(theprice.value);
        saleprice = currencyToNumber(thesale.value);
        if(saleprice>unitprice){
            alert("Sale price is greater than Retail Price!");
            thesale.setfocus();
            return false;
        } //endif
        if(saleprice<0){
            alert("Sale price cannot be negative!");
            thesale.setfocus();
            return false;
        } //endif

        hiddenCommand.value = "save";

        //check to see if there has been changes to the additional categories
        var addcats = getObjectFromID("addcats");
        var catschanged = getObjectFromID("catschanged");
        if(catschanged.value)
            addcats.value = style.prepCategories();

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

    checkStyleNumber: function(){

        var stylenumber = getObjectFromID("stylenumber");
        var excludeid = getObjectFromID("uuid");

        if(!checkUnique('tbld:7ecb8e4e-8301-11df-b557-00238b586e42', "stylenumber", stylenumber.value, excludeid.value)){

            alert("Style number must be unique.");
            stylenumber.value="";
            stylenumber.focus();

        }//endif

    },//end function checkStyleNumber


    //calculate the markup percentage based on the unit price and cost fields
    // this function is called when changes to price or cost occur.
    calculateMarkUp: function(){

        var thecost = getObjectFromID("unitcost");
        var theprice = getObjectFromID("unitprice");
        var unitcost = "";
        var unitprice = "";

        unitcost = currencyToNumber(thecost.value);
        unitprice = currencyToNumber(theprice.value);

        if(unitcost!=0 && unitprice!=0){

            var markup = getObjectFromID("markup");
            markup.value=(Math.round(((1-(unitcost/(unitprice/1.2)))*10000)/100))+"%";
//                markup.value=(Math.round((unitprice/unitcost -1)*10000)/100)+"%"

        }//endif

    },//end function calculateMarkup


    //calculates and sets the unit price based on the unit cost and the
    // markup percentage.
    calculatePrice: function(){

        var themarkup = getObjectFromID("markup");
        var thecost = getObjectFromID("unitcost");
        var theprice = getObjectFromID("unitprice");
        var unitcost = "";

        var markup = getNumberFromPercentage(themarkup.value);

        unitcost = currencyToNumber(thecost.value);

        var newnumber = (Math.round(((unitcost*(100/(100-markup)))*1.2)*100)/100).toString();

        theprice.value = numberToCurrency(newnumber);

    },//end function calculatePrice

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


    // add a category from the smart search to the "list"
    addCategory: function(){
        var display = getObjectFromID("ds-morecategories");
        var idToAdd = getObjectFromID("morecategories");

        if(idToAdd.value){

            //first we need to check to see if that id has already been added
            if(!style._duplicateCategories(idToAdd.value)){

                var newId = style._getNextId();

                var theDiv = document.createElement("div");
                theDiv.id ="AC" + newId;
                theDiv.className = "moreCats";

                var containerDiv = getObjectFromID("catDiv");

                containerDiv.appendChild(theDiv);

                theDiv.innerHTML = ' \
                    <input type="text" value="' + display.value + '" id="AC-' + newId + '" size="30" readonly="readonly"/>\
                    <input type="hidden" id="AC-CatId-' + newId + '" value="' + idToAdd.value + '" class="catIDs"/>\
                    <button type="button" id="RM-CatID-' + newId + '" class="graphicButtons buttonMinus catButtons" title="Remove Category"><span>-</span></button>\
                ';


                var buttonId = "RM-CatID-" + newId;
                var newMinusButton = getObjectFromID(buttonId);
                connect(newMinusButton, "onclick", style.removeCategory);

                //flag a change for processing
                var catschanged = getObjectFromID("catschanged");
                catschanged.value = "1";

            }//endif

        }//endif

    },//end function addCategory


    removeCategory: function(e){

        var theButton = e.src();

        var theDiv = theButton.parentNode;

        var parentDiv = theDiv.parentNode;

        parentDiv.removeChild(theDiv);

        var catschanged = getObjectFromID("catschanged");
        catschanged.value = "1"

    },//end function deleteCategory


    //calculates the next id necessary for additional categories box
    _getNextId: function(){

        var theid = 0

        var catDivs = getElementsByClassName("moreCats");
        for(var i = 0; i< catDivs.length; i++)
                if(parseInt(catDivs[i].id.substr(2)) > theid)
                        theid = parseInt(catDivs[i].id.substr(2));

        theid++;

        return theid;

    },//end function _getNextID


    //checks to see if the category has already been added,
    _duplicateCategories: function(idToCheck){

        var theIds = getElementsByClassName("catIDs");

        for(var i = 0; i < theIds.length; i++)
            if(theIds[i].value == idToCheck)
                return true;

        return false;

    },//end function _duplicateCategories


    prepCategories: function(){

//alert("step1");
        var catList = "[";

//alert("step2");
        var catIDs = getElementsByClassName("catIDs");
        for(var i=0; i<catIDs.length; i++){

            catList += '{' +
                        '"stylecategoryid" : "' +catIDs[i].value + '"' +
                        '}';

            if(i < ( catIDs.length - 1))
                catList += ",";

        }//end for

//alert("step3");
        catList += "]";
//alert(catList);

        return catList;

    }//end function prepCategories

}//end class styles



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var webDivs = new Array();
    webDivs[webDivs.length] = getObjectFromID("webstuff");

    var webLinks = new Array();
    webLinks[webLinks.length] = getObjectFromID("webenabled");

    var stylenumber = getObjectFromID("stylenumber");
    connect(stylenumber, "onchange", style.checkStyleNumber);

    var unitprice = getObjectFromID("unitprice");
    connect(unitprice, "onchange", style.calculateMarkUp);

    var unitcost = getObjectFromID("unitcost");
    connect(unitcost, "onchange", style.calculateMarkUp);

    var updatePriceButton = getObjectFromID("updatePrice");
    connect(updatePriceButton, "onclick", style.calculatePrice);

    var theButton = getObjectFromID("buttonWebPreview");
    connect(theButton, "onclick", style.editPreviewWebDesc);

//    var delThumbButton = getObjectFromID("deleteThumbButton");
//    connect(delThumbButton, "onclick", style.deleteThumb);

//    var thumbnailupload = getObjectFromID("thumbnailupload");
//    connect(thumbnailupload, "onchange", style.updateThumb);

//    var delPicButton = getObjectFromID("deletePictureButton");
//    connect(delPicButton, "onclick", style.deletePic);

//    var pictureupload = getObjectFromID("pictureupload");
//    connect(pictureupload, "onchange", style.updatePic);

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", style.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", style.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", style.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", style.cancelForm);

    var addCatButton = getObjectFromID("addCatButton");
    connect(addCatButton, "onclick", style.addCategory);

    var removeCatButtons = getElementsByClassName("catButtons");
    for(var i=0; i<removeCatButtons.length; i++)
        connect(removeCatButtons[i], "onclick", style.removeCategory);

    //set the initial focus
    var stylename = getObjectFromID("stylename");
        stylename.focus();

})
