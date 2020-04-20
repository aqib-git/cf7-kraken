import Base_Metabox from './base';

export default class Form_Metabox extends Base_Metabox {
  constructor ($) {
    super($, $('#cf7k_general_settings_metabox'));

    this.$cf7Id = this.$('select[name="cf7_id"]');
    this.$metabox = this.$('.cf7k-cpt-metabox');
    this.integrations = ['slack', 'mailchimp', 'webhook'];

    this.initIntegrations();

    this.bindEvents();
  }

  bindEvents () {
    var self = this;

    this.$('.cf7k-color-picker-field').wpColorPicker();

    this.$('.js-cf7k-integrations').change(function () {
      var selectedIntegrations = self.$(this).val();

      if (!selectedIntegrations) {
        selectedIntegrations = [];
      }

      self.integrationsVisibility(selectedIntegrations);
    });

    this.$('.js-cf7k-integrations').on('select2:select', function (e) {
      var data = e.params.data;

      setTimeout(() => {
        document.getElementById('cf7k_' + data.id + '_integration_metabox')
          .scrollIntoView({behavior: "smooth", block: "end", inline: "nearest"});
      }, 500);
    });
  }

  initIntegrations () {
    var self = this
    var $integrations = this.$('.js-cf7k-integrations');

    $integrations.select2({
      placeholder: 'Select Integration'
    });

    if (this.$cf7Id.length > 0) {
      this.$metabox.find('.integrations-row').removeClass('hidden')
      this.integrationsVisibility($integrations.val() || []);
    }
  }

  integrationsVisibility (selectedIntegrations) {
    for (var i = 0; i < this.integrations.length; i++) {
      var integration = this.integrations[i];

      if (selectedIntegrations.indexOf(integration) >= 0) {
        this.$('#cf7k_' + integration + '_integration_metabox').show();
      } else {
        this.$('#cf7k_' + integration + '_integration_metabox').hide();
      }
    }
  }
}
