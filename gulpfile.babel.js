/**
 * Internal dependencies.
 */
import config from './.gulp-config/config';

/**
 * External dependencies.
 */
import gulp from 'gulp';
import gulpLoadPlugins from 'gulp-load-plugins';
import del from 'del';
import autoprefixer from 'autoprefixer';
import cssnano from 'cssnano';
import babelify from 'babelify';
import wpPot from 'gulp-wp-pot';

/**
 * Automatically load and store all gulp plugins.
 */
const $ = gulpLoadPlugins( {
  rename: {
    'gulp-clean-css': 'cleanCSS',
  }
} );

/**
 * Task to clean.
 */
export const clean = () => {
  return del( config.clean.files );
};

/**
 * Task to build styles.
 */
export const styles = () => {
  return gulp.src( config.entries.scss )
    .pipe(
      $.sass( { outputStyle: 'expanded' } )
      .on( 'error', $.sass.logError )
    )
    .pipe( $.postcss( [ autoprefixer() ] ) )
    .pipe( gulp.dest( config.dist.css ) )
    .pipe( $.postcss( [ cssnano() ] ) )
    .pipe( $.rename( { suffix: '.min' } ) )
    .pipe( gulp.dest( config.dist.css ) );
};

/**
 * Task to build scripts.
 */
export const scripts = () => {
  return gulp.src( config.entries.js, { sourcemaps: true } )
    .pipe( $.bro( {
      transform: [
        babelify.configure( {
          presets: [ '@babel/env' ]
        } )
      ]
    } ) )
    .pipe( gulp.dest( config.dist.js ) )
    .pipe( $.uglify() )
    .pipe( $.rename( { suffix: '.min' } ) )
    .pipe( gulp.dest( config.dist.js ) );
};

/**
 * Generate POT file.
 */
export const generatePot = () => {
  const php = [
    '*.php',
    'includes/**/*.php',
    'admin/**/*.php',
  ];

  return gulp.src(php)
    .pipe(wpPot({
      domain: 'cf7-kraken',
      destFile: 'cf7-kraken.pot',
      package: 'CF7Kraken',
      lastTranslator: 'Asyncular Team <hello@asyncular.com>',
      team: 'Asyncular <hello@asyncular.com>'
    }))
    .pipe(gulp.dest('includes/languages/cf7-kraken.pot'));
};


/**
 * Task to build the project assets.
 */
export const build = ( done ) => {
  return gulp.series( 'clean', [ 'styles', 'scripts', 'generatePot' ] )( done );
};

/**
 * Task to release the project.
 */
function compile() {
  return gulp
    .src( config.release.files )
    .pipe( gulp.dest( config.release.base, {
      mode: '0755'
    } ) );
};

export const release = ( done ) => {
  return gulp.series( build, compile )( done );
};

/**
 * Task to watch sources.
 */
export const watch = () => {
  gulp.watch( config.watch.js, scripts );
  gulp.watch( config.watch.scss, styles );
};

/*
 * Export default task.
 */
export default gulp.series( build, watch );
