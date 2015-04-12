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
        $j('#loading-mask').show();
        //submit the fields through ajax call (post data)
        $j.post(url, data, function (response) {
                $j('#loading-mask').hide();
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

    /**
     * Starting point for building the editing form containing all the form elements
     * we retrieved from  the database
     */
    build: function () {
        //set the title of the form
        $j('#editFormAttributes input[name=form_title]').val(form.data.title);
        $j('#editFormAttributes input[name=form_receiver]').val(form.data.receiver);

        //set id of the form
        $j('#editFormAttributes input[name=form_id]').val(form.data.form_id);

        //set the id of the form for the publish function
        $j('#publish_form_id').val(form.data.form_id);

        //set formTemplate
        $j('select[name=form_subtemplate]').val(form.data.subtemplate);
        $j('#page-1').html($j('#' + form.data.subtemplate + 'column').html());

        //loop through the fieldsets and add them to the form
        $j.each(form.data.fieldsets, function (index, value) {
            form.addFieldSet(value);
        });

        $j('.droppable.fieldset-only').droppable(droptions);
        $j('.droppable.fieldset-droppable').droppable(fieldsetDroptions);
    },

    /**
     *
     * @param fieldset json object containing all info for the fieldset
     */
    addFieldSet: function (fieldset) {
        //check if the page where the fieldset has to be added already exists. If not add page

        if (!$j('#page-' + fieldset.pagenumber).length) {

            //add the page by cloning page 1
            var newPage = $j('#page-1').clone();

            //store the default droparea
            var droparea = $j(newPage).find('.col1 .droppable:last');

            //empty the new page
            $j(newPage).find('.col1').empty();

            //append the stored droparea
            $j(newPage).find('.col1').append(droparea);

            //set the id of the page
            $j(newPage).attr('id', 'page-' + fieldset.pagenumber);

            //append the new age to the previous page
            $j('#page-' + (fieldset.pagenumber - 1)).after(newPage);
        }

        //fetch template code from elements and clone
        var fieldsetTemplate = $j('#fieldsetTemplate').clone();

        //set id attribute of the fieldset
        fieldsetTemplate.attr('id', 'element_' + fieldset.fieldset_id);

        fieldsetTemplate.attr('name', fieldset.name);

        //set the id of the sortable element so we can refer to it when needed
        fieldsetTemplate.find('.sortable').attr('id', 'sortable_' + fieldset.fieldset_id);

        //set legend text
        $j(fieldsetTemplate).children().find('legend').text(fieldset.legend);

        //loop through the elements in this fieldset and add them
        $j(fieldset.elements).each(function (index, value) {
            newElement = form.addElement(value);

            if (fieldset.fieldset_id != value.parent_id) {
                //element is child of another element
                $j(fieldsetTemplate).find('#element_'+value.parent_id).append('<div class="childElement">'+newElement+'</div>');

            } else {
                $j(fieldsetTemplate).find('.sortable').prepend(newElement);
            }

        })

        //add the edit and remove buttons for added fieldset
        var container = '<div class="formRow" data-sort-order=' + fieldset.sort_order + ' id="element_' + fieldset.fieldset_id + '">' + $j(fieldsetTemplate).get(0).outerHTML + '<div class="btn-icon btn-edit"">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div></div>';


        //prepend the fieldset in the correct column on the correct page
        $j('#page-' + fieldset.pagenumber + ' div.column' + fieldset.column).prepend(container);

        //enable sorting for this fieldset
        $j('.formcontainer .sortable').sortable({
            helper: 'clone',
            update: function (event, ui) {
                leef.updateSortOrder(event, ui.item);
                $j(ui.item).find('.btn-icon').show();
            }
        });
    },


    /**
     * Adds an element to the specified fieldset
     * @param fieldset
     * @param element
     */
    addElement: function (element) {
        //determine type
        var elementType = element.type.toLowerCase();


        //get templateCode for element
        var elementCode = $j('#' + elementType + 'FieldTemplate').clone();


        //configure default attributes of the element
        elementCode = form.configureElement(elementCode, element);

        //configure type specific settings
        switch (elementType) {
            case 'text' :
                elementCode = form.configureInput(elementCode, element);
                break;
            case 'select':
                elementCode = form.configureSelect(elementCode, element);
                break;
            case 'radio':

                break;
            case 'group':
                elementCode = form.configureGroup(elementCode, element);
                break;
            case 'label':
                //this is only for labels that are 'standalone', not associated with an input field.
                elementCode = form.configureLabel(elementCode, element);
                break;
            case 'infobox':
                elementCode = form.configureInfoBox(elementCode, element);
                break;
            case 'yes-no':
                elementCode = form.configureYesNo(elementCode, element);
                break;
            case 'customhtml':
                elementCode = form.configureCustomHTML(elementCode, element);
                break;
        }

        //place the code in a formRow div.
        var container = '<div class="formRow" data-sort-order=' + element.sort_order + ' id="element_' + element.element_id + '"><div>' + $j(elementCode).get(0).outerHTML + '</div><div class="editbuttons"><div class="btn-icon btn-edit" id="edit-element-' + element.element_id + '">&nbsp;</div><div class="btn-icon btn-remove">&nbsp;</div><div class="btn-icon btn-duplicate">&nbsp;</div></div>';
        if(elementType!='fieldset' && elementType!='group')  {
            container +='<div class="tab"><div class="droppable hidden-droppable"></div></div>';
        }

        return container;
    },

    /**
     * Default configuration of elements
     * @param elementCode
     * @param element
     * @returns {*}
     */
    configureElement: function (elementCode, element) {

        $j(elementCode).find('label').first().text(element.label);
        $j(elementCode).find('label').first().attr('for', 'element-' + element.element_id);
        $j(elementCode).find('input').attr('name', element.name);
        $j(elementCode).find('input').attr('value', element.value);
        $j(elementCode).find('input').attr('id', 'element-' + element.element_id);
        if (element.required == '1') {
            $j(elementCode).find('input').addClass('required-entry');
        }
        if (element.validationrule) {
            $j(elementCode).find('input').addClass(element.validationrule);
        }

        if (element.placeholder != '') {
            $j(elementCode).find('input').attr('placeholder', element.placeholder);
        }
        return elementCode;
    },

    configureGroup: function (elementCode, element) {
        var code = '';
        for (var k in element.groupElements) {

            if (element.groupElements.hasOwnProperty(k)) {
                var groupElementCode = $j('#' + element.groupElements[k].type + 'FieldTemplate').clone();
                groupElementCode = form.addElement(element.groupElements[k]);
                code += groupElementCode;
            }
        }
        elementCode.find('ul.group .droppable').before(code);
        return elementCode;

    },

    /**
     * Configures a standalone label
     * @param elementCode
     * @param element
     * @returns {*}
     */
    configureLabel: function (elementCode, element) {

        $j(elementCode).find('label').attr('id', 'element-' + element.element_id);

        //label is not associated with an input element so remove the for attribute
        $j(elementCode).find('label').removeAttr('for');

        return elementCode;
    },

    configureInput: function (elementCode, element) {
        return elementCode;
    },
    configureSelect: function (elementCode, element) {
        $j(elementCode).find('label').text(element.label);
        var selectbox = $j(elementCode).find('select');

        //remove the placeholder option
        selectbox[0].firstElementChild.remove();

        selectbox.attr('id', element.element_id);
        $j(element.options).each(function () {
            $j(selectbox).append('<option id="option_' + this.option_id + '" value="' + this.value + '">' + this.label + '</option>');
        })

        $j(elementCode).find('select').html(selectbox.html());
        return $j(elementCode);
    },

    configureInfoBox: function (elementCode, element) {
        $j(elementCode).find('h4').text(element.label);
        $j(elementCode).find('div.infotext').html(element.value);

        return elementCode;
    },

    configureYesNo: function (elementCode, element) {
        $j(elementCode).find('input').each(function (index) {

            $j(this).attr('id', 'radio' + index);
            $j(this).attr('name', element.name);
        });

        return elementCode;
    },


    configureCustomHTML: function (elementCode, element) {
        $j(elementCode).find('input').each(function (index) {
            $j(this).attr('id', 'radio' + index);
            $j(this).attr('name', element.name);
        });
        $j(elementCode).find('div').empty().append(element.value);
        return elementCode;
    }
}

//perform initialization once document is loaded
$j(function () {
    form.load();
});