(function($) {
  /* Metabox */
  var CPTMetabox = {
    integrations: ['slack', 'mailchimp'],

    init: function () {
      this.initIntegrations();
      this.bindEvents();
    },

    bindEvents: function () {
      var self = this;

      $('.cf7k-color-picker-field').wpColorPicker();

      $('.js-cf7k-integrations').change(function () {
        var selectedIntegrations = $(this).val();

        if (!selectedIntegrations) {
          selectedIntegrations = [];
        }

        self.integrationsVisibility(selectedIntegrations);
      });
    },

    initIntegrations: function () {
      var $integrations = $('.js-cf7k-integrations');

      $integrations.select2({
        placeholder: 'Select Integration'
      });

      $integrations.on("select2:select", function (evt) {
        var element = evt.params.data.element;
        var $element = $(element);

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
          var $element = $(element);
          $integrations.find(":selected").after($element);
        }
      });

      this.integrationsVisibility($integrations.val() || []);
    },

    integrationsVisibility: function (selectedIntegrations) {
      for (var i = 0; i < this.integrations.length; i++) {
        var integration = this.integrations[i];

        if (selectedIntegrations.indexOf(integration) >= 0) {
          $('#cf7k_' + integration + '_integration_metabox').show();
        } else {
          $('#cf7k_' + integration + '_integration_metabox').hide();
        }
      }
    }
  }

  CPTMetabox.init();

  class Mailchimp {
    constructor() {
      this.bindEvents()
    }

    bindEvents() {
      let $metabox = $('#cf7k_mailchimp_integration_metabox')

      $metabox
        .find('input[name="mailchimp[api_key]"]')
        .keyup((e) => _.throttle(this.onApiKeyChange(e), 1000))
    }

    onApiKeyChange(event) {
      let key = event.target.value

      this.getAudienceList(key)
    }

    getAudienceList(key) {

    }
  }

  new Mailchimp()
}(jQuery));
