import Base_Metabox from './base'

export default class Webhook_Metabox extends Base_Metabox {
  constructor($) {
    super($, $('#cf7k_webhook_integration_metabox'))

    this.setElements()
    this.setData()
    this.bindEvents()
    this.run()
  }

  setData() {
    this.data = this.$.extend(true, this.data, {
      i18n: window.cf7k_admin,
      formFields: [],
      mergeFields: [],
      fieldMapping: [],
      formFieldsLoaded: false
    })
  }

  setElements() {
    this.el = this.$.extend(this.el, {
      $fieldMapping: this.el.$metabox.find('input[name="webhook[field_mapping]"]'),
      $addFieldMap: this.el.$metabox.find('.cf7k-cpt-metabox-webhook-field-mapping-add button'),
      $cf7Id: this.$('select[name="cf7_id"]')
    })
  }

  bindEvents() {
    this.el.$cf7Id
      .change((e) => this.createFieldMapping())

    this.el.$addFieldMap
      .click((e) => this.addFieldMap())

    this.el.$metabox.on(
      'change',
      '.cf7k-cpt-metabox-webhook-field-mapping-form-field',
      (e) => this.onFieldMapChange(e, 'form_field')
    )
    this.el.$metabox.on(
      'change',
      '.cf7k-cpt-metabox-webhook-field-mapping-merge-field',
      (e) => this.onFieldMapChange(e, 'merge_field')
    )

    this.el.$metabox.on(
      'click',
      '.cf7k-cpt-metabox-webhook-field-mapping-row-delete i',
      (e) => this.deleteFieldMap(e)
    )
  }

  run() {
    this.getFormFields()
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
      console.log('here ')
      this.el.$metabox
        .find('.cf7k-cpt-metabox-webhook-field-mapping-row')
        .removeClass('hidden')
    })
  }

  resetFieldMapping() {
    this.mergeFields = []
    this.formFields = []
    this.data.mergeFieldsLoaded = false
    this.data.formFieldsLoaded = false
    this.data.fieldMapping = []

    this.el.$fieldMapping.val('[]')
    this.el.$metabox
      .find('.cf7k-cpt-metabox-webhook-field-mapping-row')
      .addClass('hidden')
    this.$('.cf7k-cpt-metabox-webhook-field-mapping').html('')
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
    const prefix = `cf7k-cpt-metabox-webhook-field-mapping`
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
        <div class="${prefix}-merge-field"><input value=""></div>
        <div class="${prefix}-row-delete"><i class="dashicons dashicons-trash"></i></div>
      </div>`

    $mapping.append(tpl)

    this.insertFormFields(id)

    this.data.fieldMapping.push(fieldMap || {
      id,
      merge_field: '',
      form_field: ''
    })

    this.el.$fieldMapping.val(JSON.stringify(this.data.fieldMapping))
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
  }

  onFieldMapChange(e, field) {
    const id = this.$(e.target)
      .closest('.cf7k-cpt-metabox-mailchimp-field-mapping-row')
      .data('id')

    let fieldMap = _.find(this.data.fieldMapping, {id: id})
    fieldMap[field] = e.target.value

    this.el.$fieldMapping.val(JSON.stringify(this.data.fieldMapping))
  }

  initFieldMapping() {
    if (!this.data.formFieldsLoaded) {
      return
    }

    this.$('.cf7k-cpt-metabox-webhook-field-mapping-row i.cf7k-spin')
      .addClass('hidden')
      .siblings('*')
      .removeClass('hidden')

    const prefix = 'cf7k-cpt-metabox-webhook-field-mapping'

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
  }
}
