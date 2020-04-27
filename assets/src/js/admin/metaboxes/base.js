export default class Base_Metabox {
  constructor($, $metabox) {
    this.$ = $

    this.el = {
      $metabox: $metabox,
      $errors: $metabox.find('.cf7k-metabox-errors')
    }

    this.data = {
      errors: {}
    }

    this.el.$metabox.on('insertError', this.insertError.bind(this))
    this.el.$metabox.on('removeError', this.removeError.bind(this))
  }

  insertError(event, error) {
    if (!error) {
      this.renderError()

      return
    }

    this.data.errors[error.id] = error.text

    this.renderError()
  }

  removeError(event, error) {
    if (!error) {
      this.renderError()

      return
    }

    delete this.data.errors[error.id]

    this.renderError()
  }

  renderError() {
    if (this.data.errors && Object.keys(this.data.errors).length === 0) {
      this.el.$errors.addClass('hidden')

      return
    }

    this.el.$errors.find('ul').html('')

    for (let error in this.data.errors) {
      this.el.$errors.find('ul').append(`<li>${this.data.errors[error]}</li>`)
    }

    this.el.$errors.removeClass('hidden')
  }
}
