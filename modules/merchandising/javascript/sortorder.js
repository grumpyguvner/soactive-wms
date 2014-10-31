$(document).ready(function(){
        $('#fruit_1').button();
        $('#fruit_2').button();
        $('#fruit_3').button();
        $('#fruit_4').button();
        $('#fruit_5').button();
        $('#fruit_6').button();
        $('#fruit_7').button();
        $('#fruit_8').button();

        $('#fruit_sort').sortable({
                update: function(event, ui) {
                        var fruitOrder = $(this).sortable('toArray').toString();
                        $.get('update-sort.cfm', {fruitOrder:fruitOrder});
                }
        });
});