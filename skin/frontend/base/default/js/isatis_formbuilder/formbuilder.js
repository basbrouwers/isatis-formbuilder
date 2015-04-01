/**
 * Created by Bas Brouwers on 1/23/2015.
 */

//make jQuery play nice with Prototype
$j = jQuery.noConflict();


//define the settings for jquery droppable objects
var droptions = {
    hoverClass: 'drop-hover',
    tolerance: 'intersect',
    accept: '.draggable',
    items: 'div:not(.droppable)',

    drop: function (event, ui) {
        //clone curent droppable
        var newDropArea = $j(this).clone();

        //remove droppable functionality from element we just placed content in
        $j(this).droppable('destroy');
        $j(this).empty();
        //remove class droppable and add formRow class
        $j(this).switchClass('droppable fieldset-droppable hidden-droppable', 'formRow', 10);
        $j(this).removeClass('fieldset-only');

        leef.buildElement(this, ui);

        //check if the dropped element contains a droparea (usually in fieldset)
        //and enable it
        if (dropzone = $j(this).find('.droppable')) {
            $j(dropzone).droppable(droptions);
            $j(dropzone).droppable({accept: 'draggable'});
            $j(dropzone).droppable(droptions).appendTo($j(dropzone).parent());
        }
        //append the new droppable element to the parent object
        $j(newDropArea).droppable(droptions).appendTo($j(this).parent());
    }
};


var leef = {

    init: function () {
        $j('body').on('click', '.dependencyTrigger>label', function (event, ui) {

            
            if($j(this).prev().prop('checked')) {
                $j(this).parent().find('.dependent').slideUp();
            } else {
                $j(this).parent().find('.dependent').slideDown();
            }
        })
    }

};

//perform initialization once document is loaded
$j(function () {
    leef.init();

    var customForm = new VarienForm('formbuilder-form');

});