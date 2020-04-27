import Base_Metabox from './base'

export default class Mailchimp_Metabox extends Base_Metabox {
  constructor($) {
    super($, $('#cf7k_mailchimp_integration_metabox'))

    this.setElements()
    this.setData()
    this.bindEvents()
    this.run()
  }

  setData() {
    this.data = this.$.extend(true, this.data, {
      i18n: window.cf7k_admin,
      apiKey: '',
      formFields: [],
      mergeFields: [],
      fieldMapping: [],
      mergeFieldsLoaded: false,
      formFieldsLoaded: false
    })

    if (this.el.$apiKey.length > 0) {
      this.data.apiKey = this.el.$apiKey.val().trim()
    }
  }

  setElements() {
    this.el = this.$.extend(this.el, {
      $apiKey: this.el.$metabox.find('input[name="mailchimp[api_key]"]'),
      $audience: this.el.$metabox.find('select[name="mailchimp[audience]"]'),
      $groups: this.el.$metabox.find('select[name="mailchimp[groups][]"]'),
      $doubleOptin: this.el.$metabox.find('input[name="mailchimp[double_optin]"]'),
      $fieldMapping: this.el.$metabox.find('input[name="mailchimp[field_mapping]"]'),
      $addFieldMap: this.el.$metabox.find('.cf7k-cpt-metabox-mailchimp-field-mapping-add button'),
      $cf7Id: this.$('select[name="cf7_id"]')
    })
  }

  bindEvents() {
    this.el.$apiKey
      .blur((e) => this.onApiKeyChange(e))

    this.el.$apiKey
      .keyup((e) => this.onApiKeyChange(e))

    this.el.$audience
      .change((e) => this.onAudienceChange(e))

    this.el.$cf7Id
      .change((e) => this.createFieldMapping())

    this.el.$addFieldMap
      .click((e) => this.addFieldMap())

    this.el.$metabox.on(
      'change',
      '.cf7k-cpt-metabox-mailchimp-field-mapping-form-field',
      (e) => this.onFieldMapChange(e, 'form_field')
    )
    this.el.$metabox.on(
      'change',
      '.cf7k-cpt-metabox-mailchimp-field-mapping-merge-field',
      (e) => this.onFieldMapChange(e, 'merge_field')
    )

    this.el.$metabox.on(
      'click',
      '.cf7k-cpt-metabox-mailchimp-field-mapping-row-delete i',
      (e) => this.deleteFieldMap(e)
    )
  }

  run() {
    if (!this.data.apiKey) {
      return
    }

    this.getAudience()
  }

  onApiKeyChange(event) {
    let key = event.target.value

    if (!key || !key.trim() || key === this.data.apiKey) {
      return
    }

    this.data.apiKey = key

    this.resetAudience()
    this.getAudience()
  }

  onAudienceChange(event) {
    this.resetAudienceGroups()
    this.resetDoubleOptin()
    this.resetFieldMapping()

    if (!event.target.value) {
      return
    }

    this.getAudienceGroups()
    this.getAudienceFields()
    this.getFormFields()
    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      .removeClass('hidden')
    this.el.$doubleOptin.closest('tr').removeClass('hidden')
  }

  getAudience() {
    this.el.$audience.closest('tr').removeClass('hidden')
    this.el.$audience.siblings('i').removeClass('hidden')

    window.wp.ajax.post('cf7k_mailchimp_get_audience', {
      api_key: this.data.apiKey,
      _ajax_nonce: this.data.i18n.mailchimp.get_audience_nonce
    })
      .done((response) => {
        this.insertAudience(response.lists)
      })
  }

  getAudienceGroups() {
    this.el.$groups.select2({
      placeholder: 'Select Group'
    })

    this.el.$groups.closest('tr').find('.cf7k-spin').removeClass('hidden')
    this.el.$groups.closest('tr').removeClass('hidden')

    window.wp.ajax.post('cf7k_mailchimp_get_audience_groups', {
      api_key: this.data.apiKey,
      list_id: this.el.$audience.val(),
      _ajax_nonce: this.data.i18n.mailchimp.get_audience_groups_nonce
    }).done((response) => {
      this.insertAudienceGroups(response)
    })
  }

  getAudienceFields() {
    window.wp.ajax.post('cf7k_mailchimp_get_audience_fields', {
      api_key: this.data.apiKey,
      list_id: this.el.$audience.val(),
      _ajax_nonce: this.data.i18n.mailchimp.get_audience_fields_nonce
    }).done((response) => {
      this.data.mergeFieldsLoaded = true
      this.data.mergeFields = response || []
      this.initFieldMapping()
    })
  }

  getFormFields() {
    const id = this.el.$cf7Id.val()

    if (!id) {
      return
    }

    window.wp.ajax.post('cf7k_get_cf7_fields', {
      id: id
    }).done((response) => {
      this.data.formFieldsLoaded = true
      this.data.formFields = response || []
      this.initFieldMapping()
    })
  }

  resetAudience() {
    this.el.$audience.html('<option value="">- None -</option')
    this.el.$audience.data('value', '')

    this.resetAudienceGroups()
    this.resetDoubleOptin()
    this.resetFieldMapping()
  }

  resetAudienceGroups() {
    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-groups-row')
      .addClass('hidden')
    this.el.$groups.html('<option value="">- None -</option')
    this.el.$groups.data('value', '')
  }

  resetDoubleOptin() {
    this.el.$metabox
        .find('.cf7k-cpt-metabox-mailchimp-double-optin-row')
        .addClass('hidden')
    this.el.$doubleOptin.prop('checked', false)
  }

  resetFieldMapping() {
    this.mergeFields = []
    this.formFields = []
    this.data.mergeFieldsLoaded = false
    this.data.formFieldsLoaded = false
    this.data.fieldMapping = []

    this.el.$fieldMapping.val('[]')
    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      .addClass('hidden')
    this.$('.cf7k-cpt-metabox-mailchimp-field-mapping').html('')
  }

  insertAudience(lists) {
    let options = ''
    let value = this.el.$audience.data('value')

    for (let list of lists) {
      options += `<option value="${list.id}" ${value === list.id ? 'selected' : ''}> ${list.name}</option>`
    }

    this.el.$audience.append(options)
    this.el.$audience.siblings('i').addClass('hidden')

    if (!value) {
      return
    }

    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-groups-row')
      .removeClass('hidden')

    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-double-optin-row')
      .removeClass('hidden')

    this.el.$metabox
      .find('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      .removeClass('hidden')

    this.getAudienceGroups()
    this.getAudienceFields()
    this.getFormFields()
  }

  insertAudienceGroups(groups) {
    let options = ''
    let value = this.el.$groups.data('value') || []

    for (let id in groups) {
      options += `<option value="${id}" ${value.indexOf(id) >= 0 ? 'selected' : ''}> ${groups[id]}</option>`
    }

    this.el.$groups.html(options)
    this.el.$groups.closest('tr').find('.cf7k-spin').addClass('hidden')
    this.el.$groups.select2({
      placeholder: 'Select Group'
    })
  }

  insertAudienceFields(id) {
    const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
    let options = ''

    for (let field of this.data.mergeFields) {
      options += `<option value="${field.remote_tag}"> ${field.remote_tag}</option>`
    }

    this.$(`.${prefix} > div[data-id="${id}"]`)
      .find(`.${prefix}-merge-field select`)
      .append(options)
  }

  insertFormFields(id) {
    const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
    let options = ''

    for (let field of this.data.formFields) {
      options += `<option value="${field.name}"> ${field.name}</option>`
    }

    this.$(`.${prefix} > div[data-id="${id}"]`)
      .find(`.${prefix}-form-field select`)
      .append(options)
  }

  addFieldMap(fieldMap) {
    const prefix = `cf7k-cpt-metabox-mailchimp-field-mapping`
    const $mapping = this.$('.' + prefix)
    let id = -1

    if (!fieldMap) {
      for (let fieldMap of this.data.fieldMapping) {
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

    this.data.fieldMapping.push(fieldMap || {
      id,
      merge_field: '',
      form_field: ''
    })

    this.el.$fieldMapping.val(JSON.stringify(this.data.fieldMapping))

    this.validateFieldMapping()
  }

  deleteFieldMap(e) {
    let $row = this.$(e.target)
      .closest('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
    const id = $row.data('id')

    this.data.fieldMapping = _.without(this.data.fieldMapping, _.findWhere(this.data.fieldMapping, {id: id}))
    $row.remove()

    if (this.data.fieldMapping.length === 0) {
      this.el.$metabox.trigger('insertError', {
        id: 'fieldMapping',
        text: this.data.i18n.mailchimp.email_field_mapping
      })
    }

    this.el.$fieldMapping.val(JSON.stringify(this.data.fieldMapping))

    this.validateFieldMapping()
  }

  onFieldMapChange(e, field) {
    const id = this.$(e.target)
      .closest('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      .data('id')

    let fieldMap = _.find(this.data.fieldMapping, {id: id})
    fieldMap[field] = e.target.value

    this.el.$fieldMapping.val(JSON.stringify(this.data.fieldMapping))

    this.validateFieldMapping()
  }

  initFieldMapping() {
    if (!this.data.formFieldsLoaded || !this.data.mergeFieldsLoaded) {
      return
    }

    this.$('.cf7k-cpt-metabox-mailchimp-field-mapping-row i.cf7k-spin')
      .addClass('hidden')
      .siblings('*')
      .removeClass('hidden')

    const prefix = 'cf7k-cpt-metabox-mailchimp-field-mapping'

    let fieldMapping = JSON.parse(this.el.$fieldMapping.val() || '[]')

    for (let fieldMap of fieldMapping) {
      this.addFieldMap(fieldMap)

      this.$(`.${prefix} > div[data-id="${fieldMap.id}"]`)
        .find(`.${prefix}-merge-field select`)
        .val(fieldMap.merge_field)

      this.$(`.${prefix} > div[data-id="${fieldMap.id}"]`)
        .find(`.${prefix}-form-field select`)
        .val(fieldMap.form_field)
    }

    this.validateFieldMapping()
  }

  validateFieldMapping() {
    let hasEmailMapping = !(this.data.fieldMapping.length === 0)

    hasEmailMapping = hasEmailMapping && _.findWhere(this.data.fieldMapping, {merge_field: 'EMAIL'})

    if (hasEmailMapping) {
      this.el.$metabox.trigger('removeError', {
        id: 'fieldMapping'
      })

      return
    }

    this.el.$metabox.trigger('insertError', {
      id: 'fieldMapping',
      text: this.data.i18n.mailchimp.email_field_mapping
    })
  }
}
