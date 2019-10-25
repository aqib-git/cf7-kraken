const config = {
  entries: {
    js: [
      './assets/src/js/admin/admin.js'
    ],
    scss: [
      './assets/src/scss/admin/admin.scss'
    ]
  },
  watch: {
    js: './assets/src/js/**/*.js',
    scss: './assets/src/scss/**/*.scss',
  },
  dist: {
    base: './assets/',
    css: './assets/css/',
    js: './assets/js/'
  },
  clean: {
    files: [
      './assets/css/',
      './assets/js/',
      './release/'
    ]
  },
  release: {
    base: './release/',
    files: [
      '**',
      '!*.{lock,json,xml,js}',
      '!.gulp-config/**',
      '!README.md',
      '!build/**',
      '!node_modules/**',
      '!vendor/**',
      '!wpcs/**',
      '!assets/src/**',
      '!release/**'
    ]
  }
};

module.exports = config;
