var gulp = require('gulp');
var package = require('./package.json');
var $ = require('gulp-load-plugins')();

gulp.task('sass', function () {
    return gulp.src('./assets/src/scss/main.scss')
        .pipe($.rename('pingpong.min.css'))
        .pipe($.sourcemaps.init())
        .pipe($.sass()
            .on('error', $.sass.logError))
        .pipe($.sourcemaps.init())
        .pipe($.autoprefixer({
            browsers: ['last 2 versions', 'ie >= 9']
        }))
        .pipe($.sass({outputStyle: 'compressed'}))
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/css/'))
        .pipe($.notify({message: 'SASS complete'}));
});

gulp.task('scripts', function () {
    return gulp.src('./assets/src/js/*.js')
        .pipe($.concat('pingpong.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.sourcemaps.init())
        .pipe($.uglify())
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.notify({message: 'JS complete'}));
});

gulp.task('sass_admin', function () {
    return gulp.src('./assets/src/scss/admin/admin.scss')
        .pipe($.rename('pingpong-admin.min.css'))
        .pipe($.sourcemaps.init())
        .pipe($.sass()
            .on('error', $.sass.logError))
        .pipe($.sourcemaps.init())
        .pipe($.autoprefixer({
            browsers: ['last 2 versions', 'ie >= 9']
        }))
        .pipe($.sass({outputStyle: 'compressed'}))
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/css/'))
        .pipe($.notify({message: 'SASS Admin complete'}));
});

gulp.task('scripts_admin', function () {
    return gulp.src('./assets/src/js/admin/*.js')
        .pipe($.concat('pingpong-admin.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.sourcemaps.init())
        .pipe($.uglify())
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe($.notify({message: 'JS Admin complete'}));
});

gulp.task('default', ['sass', 'scripts', 'scripts_admin', 'sass_admin'], function () {
    gulp.watch(['./assets/src/scss/*.scss'], ['sass']);
    gulp.watch(['./assets/src/js/*.js'], ['scripts']);
    gulp.watch(['./assets/src/js/admin/*.js'], ['scripts_admin']);
    gulp.watch(['./assets/src/scss/admin/*.scss'], ['sass_admin']);
});