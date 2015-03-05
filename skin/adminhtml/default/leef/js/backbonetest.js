/**
 * Created by basb on 2/24/2015.
 */
$j = jQuery.noConflict();
(function () {
    window.Formbuilder = {
        Models: {},
        Views: {},
        Collections: {}
    };

    window.template = function (id) {
        return _.template($("#" + id).html());
    }


    /*FORM*/
    Formbuilder.Models.Form = Backbone.Model.extend({
        defaults: {
            title: '',
            template: '',
            subtemplate: '',
            action: '',
            sort_order: 0
        },
        urlRoot: $j('#getFormData').attr('action') + 'isAjax/true/form_id'
    });


    var form = new Formbuilder.Models.Form();
    form.set('title','first backbone created form');
    form.set('isAjax','true');
    form.set('form_key', window.FORM_KEY);
    form.save();

    form = new Formbuilder.Models.Form({id:93});
    form.fetch();




    

    

    
    
})
();


