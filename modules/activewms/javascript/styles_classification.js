var style = {

    submitForm: function(){

        var theform = getObjectFromID("record");
        var hiddenCommand = getObjectFromID("hiddenCommand");

        if(!validateForm(theform))
            return false;

        hiddenCommand.value = "save";

        //check to see if there has been changes to the additional sports
        var addsports = getObjectFromID("addsports");
        var sportschanged = getObjectFromID("sportschanged");
        if(sportschanged.value)
            addsports.value = style.prepSports();

        //check to see if there has been changes to the additional categories
        var addcategories = getObjectFromID("addcategories");
        var categorieschanged = getObjectFromID("categorieschanged");
        if(categorieschanged.value)
            addcategories.value = style.prepCategories();

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

    // add a sport from the smart search to the "list"
    addSport: function(){
        var display = getObjectFromID("ds-moresports");
        var idToAdd = getObjectFromID("moresports");

        if(idToAdd.value){

            //first we need to check to see if that id has already been added
            if(!style._duplicateSports(idToAdd.value)){

                var newId = style._getNextSportId();

                var theDiv = document.createElement("div");
                theDiv.id ="AS" + newId;
                theDiv.className = "moreSports";

                var containerDiv = getObjectFromID("sportDiv");

                containerDiv.appendChild(theDiv);

                theDiv.innerHTML = ' \
                    <input type="text" value="' + display.value + '" id="AS-' + newId + '" size="30" readonly="readonly"/>\
                    <input type="hidden" id="AS-SportId-' + newId + '" value="' + idToAdd.value + '" class="sportIDs"/>\
                    <button type="button" id="RM-SportID-' + newId + '" class="graphicButtons buttonMinus sportButtons" title="Remove Sport"><span>-</span></button>\
                ';


                var buttonId = "RM-SportID-" + newId;
                var newMinusButton = getObjectFromID(buttonId);
                connect(newMinusButton, "onclick", style.removeSport);

                //flag a change for processing
                var sportschanged = getObjectFromID("sportschanged");
                sportschanged.value = "1";

            }//endif

        }//endif

    },//end function addSport

    removeSport: function(e){

        var theButton = e.src();

        var theDiv = theButton.parentNode;

        var parentDiv = theDiv.parentNode;

        parentDiv.removeChild(theDiv);

        var sportschanged = getObjectFromID("sportschanged");
        sportschanged.value = "1"

    },//end function deleteSport


    //calculates the next id necessary for additional sports box
    _getNextSportId: function(){

        var theid = 0

        var sportDivs = getElementsByClassName("moreSports");
        for(var i = 0; i< sportDivs.length; i++)
                if(parseInt(sportDivs[i].id.substr(2)) > theid)
                        theid = parseInt(sportDivs[i].id.substr(2));

        theid++;

        return theid;

    },//end function _getNextSportId


    //checks to see if the sport has already been added,
    _duplicateSports: function(idToCheck){

        var theIds = getElementsByClassName("sportIDs");

        for(var i = 0; i < theIds.length; i++)
            if(theIds[i].value == idToCheck)
                return true;

        return false;

    },//end function _duplicateSports


    prepSports: function(){

        var sportList = "[";

        var sportIDs = getElementsByClassName("sportIDs");
        for(var i=0; i<sportIDs.length; i++){

            sportList += '{' +
                        '"sportid" : "' +sportIDs[i].value + '"' +
                        '}';

            if(i < ( sportIDs.length - 1))
                sportList += ",";

        }//end for

        sportList += "]";

        return sportList;

    },//end function prepSports

    // add a category from the smart search to the "list"
    addCategory: function(){
        var display = getObjectFromID("ds-morecategories");
        var idToAdd = getObjectFromID("morecategories");

        if(idToAdd.value){

            //first we need to check to see if that id has already been added
            if(!style._duplicateCategories(idToAdd.value)){

                var newId = style._getNextCategoryId();

                var theDiv = document.createElement("div");
                theDiv.id ="AS" + newId;
                theDiv.className = "moreCategories";

                var containerDiv = getObjectFromID("categoryDiv");

                containerDiv.appendChild(theDiv);

                theDiv.innerHTML = ' \
                    <input type="text" value="' + display.value + '" id="AS-' + newId + '" size="30" readonly="readonly"/>\
                    <input type="hidden" id="AC-CategoryId-' + newId + '" value="' + idToAdd.value + '" class="categoryIDs"/>\
                    <button type="button" id="RM-CategoryID-' + newId + '" class="graphicButtons buttonMinus categoryButtons" title="Remove Category"><span>-</span></button>\
                ';


                var buttonId = "RM-CategoryID-" + newId;
                var newMinusButton = getObjectFromID(buttonId);
                connect(newMinusButton, "onclick", style.removeCategory);

                //flag a change for processing
                var categorieschanged = getObjectFromID("categorieschanged");
                categorieschanged.value = "1";

            }//endif

        }//endif

    },//end function addCategory

    removeCategory: function(e){

        var theButton = e.src();

        var theDiv = theButton.parentNode;

        var parentDiv = theDiv.parentNode;

        parentDiv.removeChild(theDiv);

        var categorieschanged = getObjectFromID("categorieschanged");
        categorieschanged.value = "1"

    },//end function deleteCategory


    //calculates the next id necessary for additional categories box
    _getNextCategoryId: function(){

        var theid = 0

        var categoryDivs = getElementsByClassName("moreCategories");
        for(var i = 0; i< categoryDivs.length; i++)
                if(parseInt(categoryDivs[i].id.substr(2)) > theid)
                        theid = parseInt(categoryDivs[i].id.substr(2));

        theid++;

        return theid;

    },//end function _getNextCategoryId


    //checks to see if the category has already been added,
    _duplicateCategories: function(idToCheck){

        var theIds = getElementsByClassName("categoryIDs");

        for(var i = 0; i < theIds.length; i++)
            if(theIds[i].value == idToCheck)
                return true;

        return false;

    },//end function _duplicateCategories


    prepCategories: function(){

        var categoryList = "[";

        var categoryIDs = getElementsByClassName("categoryIDs");
        for(var i=0; i<categoryIDs.length; i++){

            categoryList += '{' +
                        '"categoryid" : "' +categoryIDs[i].value + '"' +
                        '}';

            if(i < ( categoryIDs.length - 1))
                categoryList += ",";

        }//end for

        categoryList += "]";

        return categoryList;

    }//end function prepCategories

}//end class styles

/* OnLoad Listner ---------------------------------------- */
/* ------------------------------------------------------- */
connect(window,"onload",function() {

    var saveButton = getObjectFromID("saveButton1");
    connect(saveButton, "onclick", style.submitForm);

    var saveButton = getObjectFromID("saveButton2");
    connect(saveButton, "onclick", style.submitForm);

    var cancelButton = getObjectFromID("cancelButton1");
    connect(cancelButton, "onclick", style.cancelForm);

    var cancelButton = getObjectFromID("cancelButton2");
    connect(cancelButton, "onclick", style.cancelForm);

    var addCategoryButton = getObjectFromID("addCategoryButton");
    connect(addCategoryButton, "onclick", style.addCategory);

    var addSportButton = getObjectFromID("addSportButton");
    connect(addSportButton, "onclick", style.addSport);

    var removeCategoryButtons = getElementsByClassName("categoryButtons");
    for(var i=0; i<removeCategoryButtons.length; i++)
        connect(removeCategoryButtons[i], "onclick", style.removeCategory);

    var removeSportButtons = getElementsByClassName("sportButtons");
    for(var i=0; i<removeSportButtons.length; i++)
        connect(removeSportButtons[i], "onclick", style.removeSport);


    //set the initial focus
    var stylename = getObjectFromID("ds-moresports");
        stylename.focus();

})