// gulpfile.js
const fs = require("fs");// spawnSync so gulp waits until dependencies are installed
const spawn = require("child_process").spawnSync;
try {
    // see if node_modules is present
    if (!fs.existsSync("./node_modules")) {// if not, let user know what is going on
       process.stdout.write("Installing dependencies. Please be patient.\n");      // then install dependencies
       spawn("npm", ["install"], { stdio: "inherit" });      // and finally, run gulp
       spawn("gulp", ["default"], { stdio: "inherit" });   }} catch (error) {
    console.log("gulpfile.js ", error);
 }
 ///////////////////////////////////
 // then add gulp related tasks
 ///////////////////////////////////
 const gulp = require("gulp");
 gulp.task('default', ['scripts', 'sass', 'images']);