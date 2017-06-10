var BASE_URL = 'http://blanket-example.localhost:8080';

var MuppetModel = Backbone.Model.extend({
  defaults: {
    id: null,
    name: null,
    occupation: null
  }
});

var MuppetCollection = Backbone.Collection.extend({
  url: BASE_URL + '/muppets',
  model: MuppetModel,
  parse: function(data) {
    return data.muppets;
  }
});

var MuppetsListView = Backbone.View.extend({
  el: '#muppets-app',

  initialize: function() {
    this.listenTo(this.collection, 'sync', this.render);
    this.collection.fetch();
  },

  render: function() {
    var $list = this.$('ul.muppets-list').empty();

    this.collection.each(function(model) {
      var item = new MuppetsListItemView({model: model});
      $list.append(item.render().$el);
    }, this);

    return this;
  },

  events: {
    'click .create': 'onCreate'
  },

  onCreate: function() {
    var $name = this.$('#muppet-name');
    var $job = this.$('#muppet-job');

    if ($name.val()) {
      this.collection.create({
        name: $name.val(),
        occupation: $job.val()
      });

      $name.val('');
      $job.val('');
    }
  }

});

var MuppetsListItemView = Backbone.View.extend({
  tagName: 'li',
  className: 'muppet',
  template: _.template($('#muppet-item-tmpl').html()),

  initialize: function() {
    this.listenTo(this.model, 'destroy', this.remove)
  },

  render: function() {
    var attributes = this.model.toJSON();
    attributes.base_url = BASE_URL;
    var html = this.template(attributes);
    this.$el.html(html);
    return this;
  },

  events: {
    'click .remove': 'onRemove'
  },

  onRemove: function() {
    this.model.destroy();
  }
});

var muppets = new MuppetCollection();
var muppetsView = new MuppetsListView({collection: muppets});
muppets.fetch();
