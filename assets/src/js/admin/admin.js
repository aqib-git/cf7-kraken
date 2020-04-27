class CF7_Kraken_Admin {
  constructor() {
    this.registerMetaboxes()
  }

  registerMetaboxes() {
    import('./metaboxes/form').then(module => {
      new module.default(jQuery)
    })

    import('./metaboxes/mailchimp').then(module => {
      new module.default(jQuery)
    })

    import('./metaboxes/webhook').then(module => {
      new module.default(jQuery)
    })
  }
}

new CF7_Kraken_Admin()

