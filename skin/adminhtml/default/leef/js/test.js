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
    Formbuilder.Models.Form = Backbone.Model.extend({});
    Formbuilder.Views.Form = Backbone.View.extend({
        tagName: 'form',
        template: template('formTemplate'),

        render: function () {
            this.collection.each(this.addOne,this);
            return this;
        },
        addOne: function (fieldset) {
            var fieldsetView  = new Formbuilder.Views.Fieldset({model:fieldset});
            this.$el.append(fieldsetView.render());
        }
    });

    /*FIELDSETS*/
    Formbuilder.Models.Fieldset = Backbone.Model.extend({});
    Formbuilder.Views.Fieldset = Backbone.View.extend({

        tagName: 'fieldset',
        template: template('fieldsetTemplate'),

        render: function() {
            return this.$el.html(this.template(this.model.toJSON()));
        }
    });
    Formbuilder.Collections.Fielsset = Backbone.Collection.extend({});

    /*ELEMENTS*/
    Formbuilder.Models.Element = Backbone.Model.extend({});
    Formbuilder.Views.Element = Backbone.View.extend({});

    var form = new Formbuilder.Models.Form({name: 'My Form'});

    var fieldsets = new Formbuilder.Collections.Fielsset([
        {legend: 'Fieldset 1'},
        {legend: 'Fieldset 2'}
    ]);

    var elements = new Formbuilder.Collections.Element(
        {
            type: "text",
            name: "test"
        },
        {
            type: "select",
            name: "test2"
        },
        {
            type: "radio",
            name: "test3"
        }
    );

    var fieldsetView = new Formbuilder.Views.Fieldset({collection: elements});
    var formView = new Formbuilder.Views.Form({collection: fieldsets});

    $(document.body).append(formView.render().el);
})();


