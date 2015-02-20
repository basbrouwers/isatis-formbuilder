/**
 * Created by Bas Brouwers on 1/23/2015.
 */

//make jQuery play nice with Prototype
$j = jQuery.noConflict();

var form;
var form_id;

form = {
    id: null,
    data: null,

    //initialize our form object and related functions
    load: function () {

        $j('#getFormData')
        //the action attribute of the form element determines which action will
        //be called in magento
        var url = $j('#getFormData').attr('action') + '?isAjax=true';
        var data = $j('#getFormData').serialize();

        //submit the fields through ajax call (post data)
        $j.post(url, data, function (response) {
                if (!response.error) {
                    form.data = response[0];
                    form.build();
                } else {
                    //display the error message
                    leef.displayError(response.message);
                }
                return false;
            }, 'json'
        )
    },

    build: function () {
        //set the title of the form
        $j('#editFormAttributes input[name=form_title]').val(form.data.title);

        //set id of the form
        $j('#editFormAttributes input[name=form_id]').val(form.data.form_id);

        //set pageTemplate
        $j('select[name=form_template]').val(form.data.template);

        //set formTemplate
        $j('select[name=form_subtemplate]').val(form.data.subtemplate);

        //loop through the fieldsets and add them to the form
        $j.each(form.data.fieldsets, function (index, value) {
            form.addFieldSet(value);
        });

       /* //make dropped elements sortable in main area
        $j('#formcontainerMainArea').sortable({
            helper: 'clone',
            start: function (event, ui) {
                $(ui.item).data("startindex", ui.item.index());
            },
            stop: function (event, ui) {
                console.log('adsfadfafaf');

                leef.calculateSortOrder(ui.item);
            }
        }),*/

        $j('.droppable').droppable(droptions);
    },

    /**
     *
     * @param fieldset json object containing all info for the fieldset
     */
    addFieldSet: function (fieldset) {
        //fetch template code from elements column
        fieldsetTemplate = $j('#fieldsetTemplate').clone();

        //set id attribute of the fieldset
        fieldsetTemplate.find('fieldset').attr('id',fieldset.fieldset_id);

        //set the id of the sortable element so we can refer to it when needed
        fieldsetTemplate.find('.sortable').attr('id','sortable_'+fieldset.fieldset_id);
        //set legend text
        $j(fieldsetTemplate).children().find('legend').text(fieldset.legend);

        //loop through the fields in this fieldset and add them
        $j(fieldset.elements).each(function (index, value) {
            form.addElement(fieldsetTemplate,value);
        })

        //add the edit and remove buttons
        var container  = '<div>' + $j(fieldsetTemplate).get(0).outerHTML+ '<div class=" btn-icon btn-edit" id="element-">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div></div>';

        //enable sorting for this fieldset

        $j('#formcontainer').prepend(container);
        $j('#formcontainer .sortable').sortable({helper:'clone',
            helper: 'clone',
            start: function (event, ui) {
                $(ui.item).data("startindex", ui.item.index());
            },
            stop: function (event, ui) {
                leef.updateSortOrder(event,ui);
            }});
    },

    addElement: function (fieldset,element) {
        //determine type
        var elementType= element.type.toLowerCase();

        //get templateCode for element
        elementCode = $j('#'+elementType+'FieldTemplate').clone();

        //configure type specific settings
        switch (elementType) {
            case 'input' :
                elementCode = form.configureInput(elementCode, element);
               break;
        }

        //place the code in a formRow div.
        container  = '<div class="formRow" id="element_'+element.element_id+'">' + $j(elementCode).get(0).outerHTML+ '<div class=" btn-icon btn-edit" id="element-">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div></div>';

        //add it to the active fieldset
        $j(fieldset).find('.sortable').prepend(container);


    },
    configureInput: function(elementCode, element) {
        $j(elementCode).find('label').text(element.label);
        return elementCode;
    }



}

//perform initialization once document is loaded
$j(function () {
    form.load();
});