
var styletranslation = {

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


    //calculate the markup percentage based on the unit price and cost fields
    // this function is called when changes to price or cost occur.
    calculateMarkUp: function(){

        var thecost = getObjectFromID("wholesale_price");
        var theprice = getObjectFromID("price");
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
        var thecost = getObjectFromID("wholesale_price");
        var theprice = getObjectFromID("price");
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


}//end class styles



/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var webDivs = new Array();
    webDivs[webDivs.length] = getObjectFromID("webstuff");

    var webLinks = new Array();
    webLinks[webLinks.length] = getObjectFromID("webenabled");

    var unitprice = getObjectFromID("price");
    connect(unitprice, "onchange", styletranslation.calculateMarkUp);

    var unitcost = getObjectFromID("wholesale_price");
    connect(unitcost, "onchange", styletranslation.calculateMarkUp);

    var updatePriceButton = getObjectFromID("updatePrice");
    connect(updatePriceButton, "onclick", styletranslation.calculatePrice);

    var theButton = getObjectFromID("buttonWebPreview");
    connect(theButton, "onclick", styletranslation.editPreviewWebDesc);

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", styletranslation.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", styletranslation.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", styletranslation.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", styletranslation.cancelForm);

    //set the initial focus
    var stylename = getObjectFromID("stylename");
        stylename.focus();

})
