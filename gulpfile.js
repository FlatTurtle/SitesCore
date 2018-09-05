var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var ignore = require('gulp-ignore');
var del = require('del');
gulp.task('clean', function(callback) {
    del(['public/javascript/all.js', 'public/css/common.css'], callback)

});

gulp.task('js', function() {
    return gulp.src(
        ['public/javascript/jquery.js',
         'public/javascript/jquery.datepicker.js',
         'public/javascript/jquery.fancybox.js',
         'public/javascript/jquery.timepicker.js',
         'public/javascript/carousel.js',
         'public/javascript/script.js',
         '!public/javascript/all.js'])
        .pipe(concat('all.js'))
        .pipe(ignore.exclude([ "**/*.map" ]))
        .pipe(uglify().on('error', function(e){
            console.log(e);
        }))
        .pipe(gulp.dest('public/javascript'));
});

gulp.task('css', function() {
    return gulp.src('public/css/*.scss')
        .pipe(sass({includePaths: ['public/css']}))
        .pipe(gulp.dest('public/css'));
});

gulp.task('default', gulp.series(['clean'], gulp.parallel('css', 'js')));

