var $ = jQuery

var CPTMetabox = {
  integrations: ['slack', 'mailchimp'],

  init: function () {
    this.initIntegrations();
    this.bindEvents();
  },

  bindEvents: function () {
    var self = this

    $('.cf7k-color-picker-field').wpColorPicker();

    $('.js-cf7k-integrations').change(function () {
      var selectedIntegrations = $(this).val();

      if (!selectedIntegrations) {
        selectedIntegrations = []
      }

      self.integrationsVisibility(selectedIntegrations);
    });
  },

  initIntegrations: function () {
    $('.js-cf7k-integrations').select2({
      placeholder: 'Select Integration'
    });

    this.integrationsVisibility($('.js-cf7k-integrations').val() || []);
  },

  integrationsVisibility: function (selectedIntegrations) {
    for (var i = 0; i < this.integrations.length; i++) {
      var integration = this.integrations[i];

      if (selectedIntegrations.indexOf(integration) >= 0) {
        $('#cf7k_' + integration + '_integration_metabox').show()
      } else {
        $('#cf7k_' + integration + '_integration_metabox').hide()
      }
    }
  }
}

$(document).ready(function () {
  CPTMetabox.init()
});
