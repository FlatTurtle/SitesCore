var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass');
var del = require('del');

gulp.task('default', ['clean'], function() {
        gulp.start('css', 'js') 
});

gulp.task('clean', function(callback) {
    del(['public/javascript/all.js', 'public/css/common.css'], callback)
        
});

gulp.task('js', function() {
    return gulp.src(['public/javascript/*.js', '!public/javascript/all.js'])
        .pipe(concat('all.js'))
        .pipe(uglify())
        .pipe(gulp.dest('public/javascript'));
});

gulp.task('css', function() {
    return gulp.src('public/css/*.scss')
        .pipe(sass({includePaths: ['public/css']}))
        .pipe(gulp.dest('public/css'));
});
