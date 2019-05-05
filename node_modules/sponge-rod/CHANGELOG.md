# Change Logs

## Version 3.10.1
 - Fix vulnerability of package.

## Version 3.10.0
 - Add two option in scripts which are entries_object(object) and entries_array(array)
 - Remove entries option

## Version 3.9.2
 - Fix not watching file.

## Version 3.9.1
 - Update README.md
 - Remove fonts and remain one.
 - Enhance build and unbuild schema.

## Version 3.9.0
 - Add node notifier to display message after the task process completed.
 - Remove sync delete compile file.
 - Remove src option in scripts and change to entries(webpack).
 - Remove unbuild_dir, and change to unbuild.

## Version 3.8.0 - July 26, 2018
 - Add option watch_only for scripts.
 - Update README.md

## Version 3.7.2
 - Support array on unbuild_dir.

## Version 3.7.1
 - Patch bugs

## Version 3.7.0
 - Update README
 - Update sponge config
 - Clean the code

## Version 3.6.0
 - Rename project to sponge-rod.

## Version 3.5.0
 - Update the README.md.
 - Update the wrong git url at package.json file.
 - Update the description of this package.

## Version 3.4.2
 - Remove sudo in command.

## Version 3.4.1
 - Include gulp-sass and gulp-imagemin inside of this project.
 - Just run npm install without sudo to avoid error.

## Version 3.4.0
 - Use 'wdt' as bin command instead of whole name.

## Version 3.3.0
 - Add option flatten on command build:images and build:fonts.

## Version 3.2.0
 - Show result of deleting files.

## Version 3.1.8
 - Fix calling task.
 - Update README.md.

## Version 3.1.7
 - Fix not calling plugins.sass and plugins.imagemin.

## Version 3.1.6
 - Install gulp-sass and gulp-imagemin separately because these packages require --unsafe-perm option.
 - Also to filter the it if you need to use sudo for linux or not for windows like the example below:
    - For linux `sudo npm install --save-dev --unsafe-perm gulp-sass gulp-imagemin`
    - For Windows `npm install --save-dev --unsafe-perm gulp-sass gulp-imagemin`

## Version 3.1.5
 - Forgot to remove gulp-sass gulp-imagemin inside of package.json.

## Version 3.1.4
 - Fix bug when installing package.

## Version 3.1.3
 - Add jpegtran-bin as dependency.
 - Fix issue about installing package gulp-sass.

## Version 3.1.2
 - Move gulp-sass into devDependency to install it with option --unsave-perm because that package have issue.

## Version 3.1.1
 - Trying to fix bug when registering in npm.

## Version 3.1.0
 - Rename library from web-tools to web-dev-tools because web-tools is already exist in npm registry.

## Version 3.0.0
 - Publish this library to npm.
 - Create bin command web-tools.
 - Change main file from gulpfile.js to index.js.

## Version 2.2.0
 - Allow to change debug mode.

## Version 2.1.0
 - Add feature webpack.

## Version 2.0.1
 - Fix build:dist command.

## Version 2.0.0
 - Rename package from gulp-web-dev to web-tools.
 - New file structures thats why the version move to 2.0.0.
 - Clean and readable commands.
 - Add License
