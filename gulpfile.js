var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var $ = gulpLoadPlugins();

gulp.task('sass', function(){
  return gulp.src('admin/assets/src/sass/main.scss')
    .pipe($.sass()) // Using gulp-sass
    .pipe(gulp.dest('admin/assets/css'))
});

gulp.task('watch', function(){
  gulp.watch('admin/assets/src/sass/**/*.scss', gulp.series('sass'));
  // Other watchers
})
