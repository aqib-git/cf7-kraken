import Base_Metabox from './base'

export default class Form_Metabox extends Base_Metabox {
  constructor ($) {
    super($, $('#cf7k_general_settings_metabox'));

    this.$cf7Id = this.$('select[name="cf7_id"]');
    this.$metabox = this.$('.cf7k-cpt-metabox');
    this.integrations = ['slack', 'mailchimp'];

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
  }

  initIntegrations () {
    var self = this
    var $integrations = this.$('.js-cf7k-integrations');

    $integrations.select2({
      placeholder: 'Select Integration'
    });

    $integrations.on("select2:select", function (evt) {
      var element = evt.params.data.element;
      var $element = self.$(element);

      window.setTimeout(function () {
      if ($integrations.find(":selected").length > 1) {
        var $second = $integrations.find(":selected").eq(-2);

        $element.detach();
        $second.after($element);
      } else {
        $element.detach();
        $integrations.prepend($element);
      }

      $integrations.trigger("change");
      }, 1);
    });

    $integrations.on("select2:unselect", function (evt) {
      if ($integrations.find(":selected").length) {
        var element = evt.params.data.element;
        var $element = self.$(element);
        $integrations.find(":selected").after($element);
      }
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
