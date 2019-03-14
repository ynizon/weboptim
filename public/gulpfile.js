//Tool for pictures optimization 


//Doc https://gist.github.com/LoyEgor/e9dba0725b3ddbb8d1a68c91ca5452b5
//apt-get install software-properties-common
//add-apt-repository ppa:chris-lea/node.js
//apt-get update
//apt-get install nodejs
//apt-get install npm

//npm install gulp -g
//npm install gulp-imagemin
//npm install imagemin-guetzli
//npm install gulp-plumber
//npm install yargs
//npm install gulp-uglify
//npm install gulp-clean-css --save-dev
//npm i gulp-htmlmin --save-dev
//npm install --save-dev run-sequence

const gulp = require('gulp');
const imagemin = require('gulp-imagemin');
const imageminGuetzli = require('imagemin-guetzli');
var uglify = require('gulp-uglify');
var plumber = require("gulp-plumber");
var cleanCSS = require('gulp-clean-css');
var htmlmin = require('gulp-htmlmin');
var runSequence = require('run-sequence');
var fs = require('fs');

var argv = require('yargs').argv;
var rep_src = (argv.src === undefined) ? 'tmp' : argv.src;
var rep_dest = (argv.dest === undefined) ? 'tmpdest' : argv.dest;

//Optimisation des images
var allimages = [
        rep_src+'/**/*.{jpg,JPG,jpeg,JPEG,PNG,png,SVG,svg}'
    ];
	
gulp.task('default', () =>
    gulp.src(allimages)
	.pipe(imagemin([
		imagemin.gifsicle({interlaced: true}),
		imagemin.jpegtran({progressive: true}),
		imagemin.optipng({optimizationLevel: 5}),
		imagemin.svgo({
			plugins: [
				{removeViewBox: true},
				{cleanupIDs: false}
			]
		})
	]))
	.on('error', swallowError)
	.pipe(imagemin([
        imageminGuetzli({
            quality: 85,
			nomemlimit:true
        })
    ]))
	.on('error', swallowError)
    .pipe(gulp.dest(rep_dest))
);

//Creation des css minifies
/*
gulp.task('css', function () {
  return gulp.src(rep_src + '/css/*.css')
    .pipe(cleanCSS())
    .pipe(gulp.dest(rep_dest+'/css'));
});

gulp.task('cssfile', function () {
  return gulp.src(rep_src)
    .pipe(cleanCSS())
    .pipe(gulp.dest(rep_dest));
});

//Creation des js minifies

gulp.task('js', function () {
  return gulp.src(rep_src + '/js/*.js')
    .pipe(uglify())
    .pipe(gulp.dest(rep_dest+'/js'));
});

gulp.task('jsfile', function () {
  return gulp.src(rep_src )
    .pipe(uglify())
    .pipe(gulp.dest(rep_dest));
});
*/

//Creation des html minifies
gulp.task('html', function() {
  return gulp.src(rep_src + '/*.html')
    .pipe(htmlmin({collapseWhitespace: true}))
    .pipe(gulp.dest(rep_dest));
});

gulp.task('htmlfile', function() {
  return gulp.src(rep_src )
    .pipe(htmlmin({collapseWhitespace: true}))
    .pipe(gulp.dest(rep_dest));
});

var allimages2 = [
        rep_src+'/images/*.{jpg,JPG,jpeg,JPEG,PNG,png,SVG,svg}'
    ];
var allimagescss = [
        rep_src+'/css/import/*.{jpg,JPG,jpeg,JPEG,PNG,png,SVG,svg}'
    ];
gulp.task('imagesfile', () =>	
    gulp.src(rep_src)
	.pipe(imagemin([
		imagemin.gifsicle({interlaced: true}),
		imagemin.jpegtran({progressive: true}),
		imagemin.optipng({optimizationLevel: 5}),
		imagemin.svgo({
			plugins: [
				{removeViewBox: true},
				{cleanupIDs: false}
			]
		})
	]))
	.on('error', swallowError)	
    .pipe(gulp.dest(rep_dest))
);

gulp.task('imagesjpgfile', () =>	
    gulp.src(rep_src)
	.pipe(imagemin([
		imagemin.gifsicle({interlaced: true}),
		imagemin.jpegtran({progressive: true}),
		imagemin.optipng({optimizationLevel: 5}),
		imagemin.svgo({
			plugins: [
				{removeViewBox: true},
				{cleanupIDs: false}
			]
		})
	]))
	.on('error', swallowError)	
	.pipe(imagemin([
        imageminGuetzli({
            quality: 85,
			nomemlimit:true
        })
    ]))	
	.on('error', swallowError)
    .pipe(gulp.dest(rep_dest))
);


gulp.task('all', function() {
  //runSequence('css', 'js','html','default');
  /*
  fs.writeFile(rep_dest+'/gulp.txt', '1');
  gulp.start('css');
  fs.writeFile(rep_dest+'/gulp.txt', '2');  
  gulp.start('js');
  */
  fs.writeFile(rep_dest+'/gulp.txt', '3');
  gulp.start('html');
  fs.writeFile(rep_dest+'/gulp.txt', '4');
  gulp.start('images');
  fs.writeFile(rep_dest+'/gulp.txt', '5');
  gulp.start('imagescss');
  fs.writeFile(rep_dest+'/gulp.txt', '6');
  
});


function swallowError (error) {
  // If you want details of the error in the console
  console.log(error.toString()) 
}