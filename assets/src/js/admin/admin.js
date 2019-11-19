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
      this.$metabox = $('#cf7k_mailchimp_integration_metabox')
      this.$apiKey = this.$metabox
        .find('input[name="mailchimp[api_key]"]')
      this.$audience = this.$metabox
        .find('select[name="mailchimp[audience]"]')
      this.$groups = this.$metabox
        .find('select[name="mailchimp[groups][]"]')
      this.$doubleOptin = this.$metabox
        .find('select[name="mailchimp[double_optin]"]')
      this.$fieldMapping = this.$metabox
        .find('input[name="mailchimp[field_mapping]"]')
      this.$addFieldMap = this.$metabox
        .find('.cf7k-cpt-metabox-mailchimp-field-mapping-add button')
      this.$cf7Id = $('select[name="cf7_id"]')

      this.apiKey = this.$apiKey.val().trim() || ''
      this.formFields = []
      this.mergeFields = []
      this.fieldMapping = []
      this.mergeFieldsLoaded = false
      this.formFieldsLoaded = false

      this.bindEvents()
      this.getAudience()
    }

    bindEvents() {
      this.$apiKey
        .keyup((e) => _.throttle(this.onApiKeyChange(e), 1000))

      this.$audience
        .change((e) => this.getAudienceGroups())

      this.$cf7Id
        .change((e) => this.createFieldMapping())

      this.$addFieldMap
        .click((e) => this.addFieldMap())

      this.$metabox.on(
        'change',
        '.cf7k-cpt-metabox-mailchimp-field-mapping-form-field',
        (e) => this.onFieldMapChange(e, 'form_field')
      )
      this.$metabox.on(
        'change',
        '.cf7k-cpt-metabox-mailchimp-field-mapping-merge-field',
        (e) => this.onFieldMapChange(e, 'merge_field')
      )

      this.$metabox.on(
        'click',
        '.cf7k-cpt-metabox-mailchimp-field-mapping-row-delete i',
        (e) => this.deleteFieldMap(e)
      )
    }

    onApiKeyChange(event) {
      let key = event.target.value

      this.getAudienceList(key)
    }

    getAudience() {
      window.wp.ajax.post('cf7k_mailchimp_get_audience', {
        api_key: this.apiKey
      })
        .done((response) => {
          this.insertAudience(response.lists)
        })
    }

    getAudienceGroups() {
      this.$groups.select2({
        placeholder: 'Select Group'
      })

      this.$groups.closest('tr').find('.cf7k-spin').removeClass('hidden')

      window.wp.ajax.post('cf7k_mailchimp_get_audience_groups', {
        api_key: this.apiKey,
        list_id: this.$audience.val()
      }).done((response) => {
        this.insertAudienceGroups(response)
      })
    }

    getAudienceFields() {
      window.wp.ajax.post('cf7k_mailchimp_get_audience_fields', {
        api_key: this.apiKey,
        list_id: this.$audience.val()
      }).done((response) => {
        this.mergeFieldsLoaded = true
        this.mergeFields = response || []
        this.initFieldMapping()
      })
    }

    getFormFields() {
      const id = this.$cf7Id.val()

      if (!id) {
        return
      }

      window.wp.ajax.post('cf7k_get_cf7_fields', {
        id: id
      }).done((response) => {
        this.formFieldsLoaded = true
        this.formFields = response || []
        this.initFieldMapping()
      })
    }

    insertAudience(lists) {
      let options = ''
      let value = this.$audience.data('value')

      for (let list of lists) {
        options += `<option value="${list.id}" ${value === list.id ? 'selected' : ''}> ${list.name}</option>`
      }

      this.$audience.append(options)
      this.$audience.siblings('i').addClass('hidden')

      if (value) {
        this.$metabox
        .find('.cf7k-cpt-metabox-mailchimp-groups-row')
        .removeClass('hidden')

        this.$metabox
          .find('.cf7k-cpt-metabox-mailchimp-double-optin-row')
          .removeClass('hidden')

        this.$metabox
          .find('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
          .removeClass('hidden')

        this.getAudienceGroups()
        this.getAudienceFields()
        this.getFormFields()
      }
    }

    insertAudienceGroups(groups) {
      let options = ''
      let value = this.$groups.data('value') || []

      for (let id in groups) {
        options += `<option value="${id}" ${value.indexOf(id) >= 0 ? 'selected' : ''}> ${groups[id]}</option>`
      }

      this.$groups.html(options)
      this.$groups.closest('tr').find('.cf7k-spin').addClass('hidden')
      this.$groups.select2({
        placeholder: 'Select Group'
      })
    }

    insertAudienceFields(id) {
      const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
      let options = ''

      for (let field of this.mergeFields) {
        options += `<option value="${field.remote_tag}"> ${field.remote_tag}</option>`
      }

      $(`.${prefix} > div[data-id="${id}"]`)
        .find(`.${prefix}-merge-field select`)
        .append(options)
    }

    insertFormFields(id) {
      const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
      let options = ''

      for (let field of this.formFields) {
        options += `<option value="${field.name}"> ${field.name}</option>`
      }

      $(`.${prefix} > div[data-id="${id}"]`)
        .find(`.${prefix}-form-field select`)
        .append(options)
    }

    addFieldMap(fieldMap) {
      const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
      const $mapping = $('.' + prefix)
      let id = -1

      if (!fieldMap) {
        for (let fieldMap of this.fieldMapping) {
          if (id < fieldMap.id) {
            id = fieldMap.id
          }
        }

        id += 1
      } else {
        id = fieldMap.id
      }

      let tpl = `
        <div class="${prefix}-row" data-id="${id}">
          <div class="${prefix}-form-field"><select><option value="">- None -</option></select></div>
          <div class="${prefix}-merge-field"><select><option value="">- None -</option></select></div>
          <div class="${prefix}-row-delete"><i class="dashicons dashicons-trash"></i></div>
        </div>`

      $mapping.append(tpl)

      this.insertFormFields(id)

      this.insertAudienceFields(id)

      this.fieldMapping.push(fieldMap || {
        id,
        merge_field: '',
        form_field: ''
      })

      this.$fieldMapping.val(JSON.stringify(this.fieldMapping))
    }

    deleteFieldMap(e) {
      let $row = $(e.target)
        .closest('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      const id = $row.data('id')

      this.fieldMapping = _.without(this.fieldMapping, _.findWhere(this.fieldMapping, {id: id}))
      $row.remove()

      this.$fieldMapping.val(JSON.stringify(this.fieldMapping))
    }

    onFieldMapChange(e, field) {
      const id = $(e.target)
        .closest('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
        .data('id')

      let fieldMap = _.find(this.fieldMapping, {id: id})
      fieldMap[field] = e.target.value

      this.$fieldMapping.val(JSON.stringify(this.fieldMapping))
    }

    initFieldMapping() {
      if (!this.formFieldsLoaded || !this.mergeFieldsLoaded) {
        return
      }

      $('.cf7k-cpt-metabox-mailchimp-field-mapping-row i.cf7k-spin')
        .addClass('hidden')
        .siblings('*')
        .removeClass('hidden')

      const prefix = 'cf7k-cpt-metabox-mailchimp-field-mapping'
      let fieldMapping = JSON.parse(this.$fieldMapping.val() || '[]')

      for (let fieldMap of fieldMapping) {
        this.addFieldMap(fieldMap)

        $(`.${prefix} > div[data-id="${fieldMap.id}"]`)
          .find(`.${prefix}-merge-field select`)
          .val(fieldMap.merge_field)

        $(`.${prefix} > div[data-id="${fieldMap.id}"]`)
          .find(`.${prefix}-form-field select`)
          .val(fieldMap.form_field)
      }
    }
  }

  new Mailchimp()
}(jQuery));
