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
    version: '1.0.0',
    activeField: null,//field in the form that is being edited
    messageTimer: null,
    newElementAdded: false,
    validationRules: [],
    //initialize our leef object and related functions
    init: function () {

        //add the validation rules to the editor
        leef.addValidationRules();

        //make dropped elements sortable in main area
        $j('.formcontainerMainArea').sortable({
            helper: 'clone',
            update: function (event, ui) {
                leef.updateSortOrder(event, ui.item);
            }
        }),

            $j('body').on('click','.dependencyTrigger label', function(event, ui){
                $j(this).siblings('.dependent').slideToggle();
            })

            //add a sortupdate event so we can trigger the sorting update event manually
            $j('.sortable').on('sortupdate', function (event, element) {
                leef.updateSortOrder('', element);
            }),
            //enable draggables
            $j('.draggable').draggable({revert: "invalid", helper: "clone"}),

            //enable delete button
            $j('body').on('click', '.btn-remove', function () {
                leef.removeElement(this);
            }),

            //enable duplicate button
            $j('body').on('click', '.btn-duplicate', function () {
                leef.duplicateElement(this);
            }),
            //enable the edit button
            $j('body').on('click', '.btn-edit', function () {
                leef.editElement(this, function (e) {
                });
            }),

            //enable the plus button
            $j('body').on('click', '.btn-plus', function () {
                leef.addOption(this);
            }),

            //enable the plus button for radio group
            $j('body').on('click', '.btn-plus-radio', function () {
                leef.addRadioToGroup(this);
            }),

            $j('body').on('click', '.btnAddpage', function () {
                leef.addPage();
            }),

            $j('#anchor-content').on('click', '.box label', function (event, ui) {
                $j(this).parent().parent().siblings('.editbuttons').slideToggle().css('display', 'inline-block');
            }),

            $j('#form_subtemplate_select').on('change', function () {
                leef.setSubTemplate($j(this).val());
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

                //submit the form data through ajax call (post data)
                $j.post(url, data, function (response) {

                        if (!response.error) {
                            //loop through response and set id's  where necessary
                            $j.each(response, function (key, value) {
                                //check if an id field is available
                                if (key.match(/_id/)) {
                                    //Update the id field and field type
                                    $j('#' + key).val(value);
                                    if (leef.activeField != null) {
                                        leef.activeField.attr('id', leef.activeField.attr('data-element-type') + value);
                                        leef.activeField.closest('.formRow').attr('id', 'element_' + value);
                                    }
                                }
                            })

                            //close featherlight box if it exists
                            if ($j.featherlight.current()) {
                                $j.featherlight.current().close();
                                leef.displayMessage('Element succesvol opgeslagen');
                                //was this a newly added element or an existing element that was edited?
                                //if new, update the sortorder
                                if (leef.newElementAdded) {
                                    leef.newElementAdded = false;
                                    leef.updateSortOrder('', leef.activeField);
                                }
                            } else {
                                //we submitted the form
                                leef.displayMessage(response.message);

                                //chcek if we need to add the first column div. This is only needed when building a new form.
                                if ($j('#page-1').find($j('.column' + response.form_subtemplate)).length == 0) {
                                    //set the columns in the main form area to the number that was chosen for the form
                                    $j('#page-1').empty();
                                    $j('#page-1').append($j('#' + response.form_subtemplate + 'column').html());

                                    //configure droppable area
                                    $j('.droppable').droppable(droptions);
                                }
                            }
                        } else {
                            leef.displayError(response.message);
                        }
                    }
                )

                return false;
            })
    },

    addValidationRules: function () {
        //gather the validation rules from the Validation object
        var options = '<option value=""></option>';
        for (var method in Validation.methods) {

            if (method.indexOf('validate') != -1) {
                //add the validationrules to the editor
                options += '<option value="' + method.toString() + '">' + method.toString() + '</option>';
            }
        }
        $j('#validationrule').append(options);
    },

    addPage: function () {
        //add the page
        var newPage = $j('#page-1').clone();

        ////remove all content except for the droparea
        var droppable = $j('.droppable').first();
        $j(newPage).html('').append(droppable);

        var pagenumber = $j('div.formcontainer').length + 1;
        $j('#page-' + (pagenumber - 1)).find('.btnAddPageWrap').remove();

        $j(newPage).attr('id', 'page-' + pagenumber);

        $j('#page-' + (pagenumber - 1)).after(newPage);
    },

    /**
     *
     * @param errorMessage string
     */
    displayError: function (errorMessage) {

        if (leef.messageTimer) {
            clearTimeout(leef.messageTimer);
        }
        $j('#error').html(errorMessage);
        $j("#error").fadeIn();
        leef.messageTimer = setTimeout(function () {
            $j('#error').fadeOut();
        }, 3000);
    },

    displayMessage: function (message) {
        if (leef.messageTimer) {
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

        $j(droppedElement).find('.box').removeAttr('id');

        var container = '<div>' + droppedElement + '</div><div class="editbuttons"><div class="btn-icon btn-edit">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div><div class="btn-icon btn-duplicate">&nbsp;</div></div>';


        if (droppedElement.indexOf('fieldset') == -1 && droppedElement.indexOf('group')) {
            container += '<div class="tab sortable"><div class="droppable hidden-droppable"></div></div>';
            //new element added so we need to make sure the sortorder is calculated
            //newElementAdded is used after submitting the editform to determine if sorting is needed
            leef.newElementAdded = true;
        }
        $j(container).appendTo(droparea);


        //make sure all items are sortable, including the newly added item
        $j(droparea).find('.sortable').sortable({
            helper: 'clone',
            update: function (event, ui) {
                $j(this).sortable("toArray");
                leef.updateSortOrder(event, ui.item);
            }
        });


        //open the edit window so user can configure the element
        //default argument for the edit function is the edit button that was clicked.
        //In this case no button was clicked so we pas the edit-button 'by hand'
        setTimeout(function () {
            leef.editElement($j(droparea).children().find('.btn-edit'));
        }, 200);
    },

    /**
     * Removes element from form by removing it's parent container
     * but only after the element has succesfully been removed from the database
     *
     * @param element object is the delete button that was clicked
     */
    removeElement: function (element) {

        //update the database to remove the element
        var url = $j('#removeElementForm').attr('action');
        var formData = $j('#removeElementForm').serializeArray();

        //formRow contains the element id. format is: element_123
        var element_id = $j(element).closest('.formRow').attr('id');

        formData.push({name: 'element_id', value: element_id});

        $j.post(url, formData, function (response) {
            if (!response.error) {
                ////removes the input element and siblings by removing the parent
                $j(element).closest('.formRow').remove();
                leef.displayMessage(response.message);
            } else {
                leef.displayError(response.message);
            }

        })

    },

    duplicateElement: function (element) {
        var newRow = $j(element).closest('.formRow').clone();
        //remove the id attribute of the row div
        $j(newRow).removeAttr('id');
        //remove the id attribute of the input element
        $j(newRow).find('.formElement').removeAttr('id');
        $j(newRow).find('.formElement').removeAttr('name');
        //insert the new element after the item that was clicked
        $j(element).closest('.formRow').after(newRow);

        //open the edit window to add the new fields

    },

    /**
     *
     * @param button the edit button that was clicked. Determines what form elements needs to be editted
     * @param newElement Indicated if this is a newly added element, dropped into the form. If so we need to update sort order
     */
    editElement: function (button) {
        //determine element type and determine what fields to display

        var element = $j(button).parent().parent().find('.formElement');
        console.log(element);
        

        leef.activeField = element;

        var elementType = element.attr('data-element-type');
        //set the parent_id of the element to the id parent element
        var parentElementType = leef.determineParent(element, elementType);

        //init formfield editor
        formfields = leef.initFormFieldEditor(elementType,parentElementType);

            var data = $j('#formFieldEditor').clone(true);
            data.find('#elementSubmit').before(formfields);
            data = data.show();

            //assign our update functions to featherlights keydown event handler
            $j.featherlight(data, {
                onKeyDown: function (event) {
                    leef.updateFormElement(event.target);
                },
                onClose: function () {
                    data.destroy();
                }
            });
    },

    /**
     *
     * @param element
     * @param elementType
     * @returns string
     */
    determineParent: function (element, elementType) {
        switch (elementType) {
            case 'fieldset' :
                //fieldsets always have the form as parent so fetch the form_id
                $j('#parent_id').val($j('#form_id').val());
                break;
            default :
                //other elements are always child of an element, be it a fieldset or another formelement
                $j('#parent_id').val($j(element).closest('.formRow').parent().closest('.formRow').attr('id'));
                parentElementType = $j(element).closest('.formRow').parent().closest('.formRow').find('.formElement').attr('data-element-type');
                $j('#parent_type').val(parentElementType);
                return parentElementType;
                break;
        }
    },

    /**
     * Function adds an extra option to a dropdown.
     * @param button
     */
    addOption: function (button) {
        var option = '<input type="text" name="option[]" /><br />';
        $j(option).insertBefore(button);
        //add the new option field to the selectbox
    },

    addRadioToGroup: function (button) {

    },

    /**
     * Builds the editor for altering the attributes of an input field
     * @param {jQuery}elementType
     * @param callback
     */
    initFormFieldEditor: function (elementType,parentElementType, callback) {
        var elementsToShow = '#' + elementType.toLowerCase() + 'FormElements';

        var formfields = $j("#generalFormElements").clone();

        //set id of element in hidden field of the form
        if (leef.newElementAdded) {
            $j('#element_id').val('');
        } else {
            $j('#element_id').val(leef.activeField.closest('.formRow').attr('id'));
        }

        //set the element type
        $j('#element_type').val(elementType);


        $j('#elementName').attr('value',leef.getElementName());

        //check if field is required
        if (leef.activeField.find(':input').first().hasClass('required-entry')) {
            $j('#element_required').attr('checked', true);
        }

        switch (elementType) {
            case 'fieldset':
                //set the columnumber where the fieldset is placed
                $j('#fieldset-column').val(leef.activeField.closest('.formcolumn').attr('data-fieldset-column'));
                $j('#pagenumber').val(leef.activeField.closest('.formcontainer').attr('id').substr(5));
                //get legend text
                $j('#elementLegend').attr('value', leef.activeField.find('legend').text());
                formfields.append($j('#legendEditField').clone());
                break;

            case 'select':
                //label field
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());

                $j('#selectoptions').empty();

                $j('#selectoptions').append('<span><strong>Options</strong></span><br>');

                //get the options from the selectbox
                leef.activeField.find('select > option').each(function () {
                    $j('#selectoptions').append('<input class="formElement selectoption"  name="option[' + $j(this).attr('id') + ']" value="' + $j(this).text() + '|' + $j(this).val() + '" type="text"/><br />');
                });

                //add the plus button so user can add more options
                $j('#selectoptions').append('<div class="btn-plus">&nbsp;</div>');
                $j('#selectoptions').show();

                formfields.append($j('#selectEditField').clone());
                break;

            case 'label':
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());
                $j('#elementName').attr('value',leef.activeField.find('label').first().attr('name'));
                break;

            case 'infobox':
                $j('#infoText').text(leef.activeField.find('div.infotext').text());
                $j('#infoTextTitle').attr('value', leef.activeField.find('h4').text());
                formfields.append($j('#infoboxEditField').clone());
                break;

            case 'text':
                //label field
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());

                //name field
                $j('#elementPlaceholder').attr('value', leef.activeField.find(':input').first().attr('placeholder'));
                formfields.append($j('#placeholderEditField').clone());

                //value field
                $j('#elementValue').attr('value', leef.activeField.find(':input').first().val());
                formfields.append($j('#valueEditField').clone());
                break;

            case 'textarea':
                //label field
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());
                break;

            case 'date':
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());
                break;

            case 'radio':
            case 'checkbox':
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());

                $j('#elementValue').attr('value', leef.activeField.find(':input').first().val());
                formfields.append($j('#valueEditField').clone());
                break;

            case 'customhtml':
                formfields.append($j('#customHTMLEditField').clone());
                break;

            case 'group':
                $j('#elementLabel').attr('value', leef.activeField.find('label').text());
                formfields.append($j('#labelEditField').clone());
                break;

            case 'yes-no':
                //get label text
                $j('#elementLabel').attr('value', leef.activeField.find('label').first().text());
                formfields.append($j('#labelEditField'));
                break;
        }
        formfields.prepend($j('#generalEditFields').clone());

        if(parentElementType=='checkbox' || parentElementType=='radio') {
            formfields.append($j('#parentDependencyEditField').clone());
        }




        //$j(formfields).show();

            return formfields;
    },


    getElementName: function(){
        var name = '';
        name = leef.activeField.find(':input').first().attr('name');
        if(!name) {
            name = leef.activeField.find('div').first().attr('name');
        }

        return name;
    },

    /**
     *
     * @param  formField string - Field that triggered the keyDown event
     */
    updateFormElement: function (formField) {
        //determine field to update

        var field = $j(formField).attr('name').substr(8);

        switch (field) {
            case 'label':
                leef.updateLabel(formField);
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
            case 'placeholder':
                leef.updatePlaceholder(formField);
                break;
        }
    },

    updatePlaceholder: function (placeholderField) {
        leef.activeField.find('input').attr('placeholder', $j(placeholderField).val());
    },


    /**
     *  Updates the Label of the input field
     * @param {jQuery} labelField
     */
    updateLabel: function (labelField) {
        //find the label for the inputfield and update it's text value
        leef.activeField.find('label').first().text($j(labelField).val());
    },

    /**
     *  Updates the value of the input field
     * @param {jQuery} valueField
     */
    updateValue: function (valueField) {
        leef.activeField.find('input').val($j(valueField).val());
    },

    /**
     * Updates the name attribute of the field specified in leef.activeField
     * @param {jQuery} nameField
     */
    updateName: function (nameField) {
        leef.activeField.find(':input').first().attr('name', $j(nameField).val());
    },

    /**
     * Updates the text value of the legend tag
     * @param {jQuery} nameField
     */
    updateLegend: function (nameField) {
        leef.activeField.find('legend').text($j(nameField).val());
    },

    /**
     *
     * @param data
     */
    updateSortOrder: function (event, item) {
        //convert the parent div of the dragged element to an array containing the sorted elements
        data = $j(item).closest('.sortable').sortable('toArray');

        var url = $j('#sortOrderForm').attr('action');

        var formData = $j('#sortOrderForm').serializeArray();

        formData.push({name: 'sortOrder', value: data});

        $j.post(url, formData, function (response) {
            if (!response.error) {
                leef.displayMessage('Sortering succesvol opgeslagen');
            } else {
                leef.displayError(response.message);
            }
        })

    },

    setSubTemplate: function (value) {
        $j('#page-1').html($j('#' + value).html());
    }


};

//perform initialization once document is loaded
$j(function () {
    leef.init();
});