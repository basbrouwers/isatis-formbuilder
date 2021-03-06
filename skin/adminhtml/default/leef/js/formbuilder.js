/**
 * Created by Bas Brouwers on 1/23/2015.
 */

//make jQuery play nice with Prototype
$j = jQuery.noConflict();


//define the settings for jquery droppable objects
var droptions = {
    hoverClass: 'drop-hover',
    tolerance: 'touch',
    accept: '.draggable',
    //exclude: '.droppable',
    //cancel: '.droppable',
    items:'div:not(.droppable)',
    drop: function (event, ui) {
        //clone curent droppable
        var newDropArea = $j(this).clone();

        //remove droppable from element we just placed content in
        $j(this).droppable('destroy');

        //remove class droppable and add formRow class
        $j(this).switchClass('droppable', 'formRow', 10);

        leef.buildElement(this, ui);

        //check if the dropped element contains a droparea (usualy in fieldset)
        //and enable it
        if (dropzone = $j(this).find('.droppable')) {
            $j(dropzone).droppable(droptions).appendTo($j(dropzone).parent());
        }
        //append the element to the parent oobject
        $j(newDropArea).droppable(droptions).appendTo($j(this).parent());
    }
};


var leef = {
    version: '1.0.0',
    activeField: null,//field in the form that is being edited
    messageTimer:null,

    //initialize our leef object and related functions
    init: function () {

        //make dropped elements sortable in main area
        $j('#formcontainerMainArea').sortable({
            helper: 'clone',
            start: function (event, ui) {
                $(ui.item).data("startindex", ui.item.index());
            },
            stop: function (event, ui) {
                leef.updateSortOrder(ui.item);
            }
        }),

            //enable draggables
            $j('.draggable').draggable({revert: "invalid", helper: "clone"}),

            //configure droppable area
            $j('.droppable').droppable(droptions),

            //enable delete button
            $j('body').on('click', '.btn-remove', function () {
                leef.removeElement(this);
            }),

            //enable the edit button
            $j('body').delegate('.btn-edit', 'click', function () {
                leef.editElement(this);
            }),

            //enable the edit button
            $j('body').on('click', '.btn-plus', function () {
                leef.addOption(this);
            }),

            //check if a form is submitted and perform ajax call to save the data
            $j(document).on('submit', 'form', function () {
                //don't handle forms that have nonAjax class
                if ($j(this).hasClass('nonAjax')) {
                    //return true so default event fires and form is submitted
                    return true;
                }
                //the action attribute of the form element determines which action will
                //be called in magento
                var url = $j(this).attr('action') + 'isAjax=true';
                var data = $j(this).serialize();

                //submit the fields through ajax call (post data)
                $j.post(url, data, function (response) {

                        if (!response.error) {
                            //loop through response and set id's  where necessary
                            $j.each(response, function (key, value) {
                                //check if an id field is available
                                if (key.match(/_id/)) {
                                    //id fields have an id value equal to their name
                                    //Update the id field
                                    $j('#' + key).val(value);
                                    if (leef.activeField != null) {
                                        leef.activeField.attr('id', value);
                                        leef.activeField.closest('.formRow').attr('id','element_'+value);
                                    }
                                }
                            })

                            //close featherlight box if it exists
                            if ($j.featherlight.current()) {
                                $j.featherlight.current().close();
                            } else {
                                //we submitted the form so show succes icon
                                $j("#editFormFieldSuccess").fadeIn();
                                setTimeout(function () {
                                    $j('#editFormFieldSuccess').fadeOut();
                                }, 3000);
                            }
                        } else {
                            leef.displayError(response.error);
                        }
                    }
                )

                return false;
            })
    },

    /**
     *
     * @param errorMessage string
     */
    displayError: function (errorMessage) {
        $j('#error').html(errorMessage);
    },

    displayMessage: function (message) {
        if(leef.messageTimer){
            clearTimeout(leef.messageTimer);
        }
        $j('#message').html(message);
        $j("#message").fadeIn();
        leef.messageTimer = setTimeout(function () {
            $j('#message').fadeOut();
        }, 3000);


    },
    /**
     * Function builds the element after being dropped. Adds edit en delete buttons
     * @param droparea
     * @param ui
     */
    buildElement: function (droparea, ui) {
        //build element container
        var droppedElement = ui.draggable.html();

        var container = '<div>' + droppedElement + '<div class=" btn-icon btn-edit" id="element-">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div></div>';

        $j(container).appendTo(droparea);

        //make sure all items are sortable, including the new added item
        $j(droparea).find('.sortable').sortable({
            helper: 'clone',
            start: function(event,ui) {
                $j(ui.item).data("startindex", ui.item.index());
            },
            stop: function (event, ui) {
                //$j(this).sortable( "toArray")
                leef.updateSortOrder(event,ui);
            }
        });

    },


    /**
     * Removes element from form by removing it's parent container
     * @param element
     */
    removeElement: function (element) {


        //update the database to remove the element
        var url= $j('#removeElementForm').attr('action');
        var formData = $j('#removeElementForm').serializeArray();
        //formRow contains the element id. format is: element_123
        var element_id = $j(element).closest('.formRow').attr('id').substr(8);

        formData.push({name:'element_id', value:element_id});


        $j.post(url, formData,function (response) {
            if(!response.error) {
                ////removes the input element and siblings by removing the parent
                $j(element).closest('.formRow').remove();
                leef.displayMessage('Element succesvol verwijderd.');
            } else {
                leef.displayError(response.message);
            }

        })

    },

    editElement: function (button) {
        //determine element type and determine what fields to display
        var element = $j(button).siblings().find('.formElement');

        leef.activeField = element;

        var elementType = element[0].tagName;

        //set the parent_id of the element to the id of the form
        leef.determineParent(element);

        //hide all fields in the formFieldEditor
        $j('#editForm div').each(function () {
            $j(this).hide();
        });

        //init formfield editor
        leef.initFormFieldEditor(elementType, function () {
            data = $j('#formFieldEditor').clone(true).html();

            //assign our update functions to featherlights keydown event handler
            $j.featherlight(data, {
                onKeyDown: function (event) {
                    leef.updateFormElement(event.target);
                },
                onClose: function () {
                    data.destroy();
                }
            });
        });
    },

    determineParent: function (element) {
        var elementType = element[0].tagName;

        switch (elementType.toLocaleLowerCase()) {
            case 'fieldset' :
                //fieldsets always have the form as parent so fetch the form_id
                $j('#parent_id').val($j('#form_id').val());
                break;
            default :
                //other elements are always part of a fieldset
                $j('#parent_id').val($j(element).closest('fieldset').prop('id'));
                break;
        }
    },

    addOption: function (button) {
        var option = '<input type="text" name="option[]" /><br />';
        $j(option).insertBefore(button);
        //add the new option field to the selectbox
    },

    /**
     * Builds the editor for altering the attributes of an input field
     * @param {jQuery}elementType
     * @param callback
     */
    initFormFieldEditor: function (elementType, callback) {
        var elementsToShow = '#' + elementType.toLowerCase() + 'FormElements';
        //set id of element in hidden field of the form
        $j('#element_id').val(leef.activeField.prop('id'));

        //set the element type
        $j('#element_type').val(elementType);

        switch (elementType.toLowerCase()) {
            case 'input':
                //get value of the name attribute and fill form field
                $j('#elementName').attr('value',leef.activeField.attr('name'));

                //get field value and  fill value field in edit form
                $j('#elementValue').attr('value',leef.activeField[0].value);

                //get label text
                $j('#elementLabel').attr('value',leef.activeField.parent().find('label').text());
                break;

            case 'select':
                $j('#element_type').val(elementType.toLowerCase());

                //get attribute name value
                $j('#elementName').attr('value',leef.activeField.attr('name'));
                //get label value
                $j('#elementLabel').attr('value',leef.activeField.closest('label').text());

                //get the options from the selectbox
                for (var i = 0; i < leef.activeField[0].options.length; i++) {
                    $j('#selectFormElements').append('<input class="formElement"  name="options[]"value="' + leef.activeField[0].options[i].text + '" type="text"/><br />');
                }
                //add the plus button so user can add more options
                $j("#selectFormElements").append('<div class="btn-plus">&nbsp;</div>');
                break;
        }


        $j("#generalFormElements").show();
        $j(elementsToShow).show();


        if ($j.isFunction(callback)) {
            callback.call();
        }
    },

    /**
     *
     * @param formField Field that triggered the keyDown event
     */
    updateFormElement: function (formField) {
        //determine field to update


        var field = $j(formField).attr('name').substr(8);

        switch (field) {
            case 'label':
                leef.updateLabel(formField)
                break;
            case 'name':
                leef.updateName(formField);
                break;
            case 'value':
                leef.updateValue(formField);
                break;
            case 'legend':
                leef.updateLegend(formField);
                break;
        }
    },

    /**
     *  Updates the Label of the input field
     * @param {jQuery} labelField
     */
    updateLabel: function (labelField) {
        //find the label for the inputfield and update it's text value
        leef.activeField.parent().find('label').text($j(labelField).val());
    },

    /**
     *  Updates the value of the input field
     * @param {jQuery} valueField
     */
    updateValue: function (valueField) {
        leef.activeField.val($j(valueField).val());
    },

    /**
     * Updates the name attribute of the field specified in leef.activeField
     * @param {jQuery} nameField
     */
    updateName: function (nameField) {
        leef.activeField.attr('name', $j(nameField).val());
    },

    /**
     * Updates the text value of the legend tago
     * @param {jQuery} nameField
     */
    updateLegend: function (nameField) {
        leef.activeField.find('legend').text($j(nameField).val());
    },

    /**
     *
     * @param data
     */
    updateSortOrder: function (event,ui) {

        //convert the parent div of the dragged element to an array containing the sorted elements
        data =$j('#'+ui.item.parent().attr('id')).sortable('toArray');

        
        var url= $j('#sortOrderForm').attr('action');

        var formData = $j('#sortOrderForm').serializeArray();

        formData.push({name:'sortOrder', value:data});

        $j.post(url, formData,function (response) {
            if(!response.error) {
                leef.displayMessage('Sortering succesvol opgeslagen');
            } else {
                leef.displayError(response.message);
            }

        })

    }

};


//perform initialization once document is loaded
$j(function () {
    leef.init();
});