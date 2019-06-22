var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var $ = gulpLoadPlugins();

gulp.task('sass', function() {
  return gulp.src('admin/assets/src/sass/main.scss')
    .pipe($.sass()) // Using gulp-sass
    .pipe(gulp.dest('admin/assets/css'))
});

gulp.task('scripts', function() {
  return gulp.src('admin/assets/src/js/main.js')
    .pipe($.concat('main.min.js'))
    .pipe($.uglify())
    .pipe(gulp.dest('admin/assets/js'));
});

gulp.task('build', gulp.parallel('sass', 'scripts'));

gulp.task('watch', function() {
  gulp.watch('admin/assets/src/sass/**/*.scss', gulp.series('sass'));
  gulp.watch('admin/assets/src/js/main.js', gulp.series('scripts'));
});
