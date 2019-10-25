// var gulp = require('gulp');
// var gulpLoadPlugins = require('gulp-load-plugins');
// var $ = gulpLoadPlugins();

// gulp.task('sass', function() {
//   return gulp.src('admin/assets/src/sass/main.scss')
//     .pipe($.sass()) // Using gulp-sass
//     .pipe(gulp.dest('admin/assets/css'))
// });

// gulp.task('scripts', function() {
//   return gulp.src('admin/assets/src/js/main.js')
//     .pipe($.concat('main.min.js'))
//     .pipe($.uglify())
//     .pipe(gulp.dest('admin/assets/js'));
// });

// gulp.task('build', gulp.parallel('sass', 'scripts'));

// gulp.task('watch', function() {
//   gulp.watch('admin/assets/src/sass/**/*.scss', gulp.series('sass'));
//   gulp.watch('admin/assets/src/js/main.js', gulp.series('scripts'));
// });

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
 * Task to build the project assets.
 */
export const build = ( done ) => {
  return gulp.series( 'clean', [ 'styles', 'scripts' ] )( done );
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
