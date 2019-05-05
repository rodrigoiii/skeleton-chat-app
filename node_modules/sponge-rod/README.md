# Sponge Rod

## What is Sponge Rod?

Sponge Rod is a cli tool that build your assets css, js and images.

## Installation
 - Use `npm install --save-dev sponge` command in your terminal to install the dependency.
 - Also `gulp-cli` must be installed in your computer. To do that, use `npm install -g gulp-cli`.

## Commands
 - `node node_modules/.bin/sponge [-h|--help]` - Show command list.
 - `node node_modules/.bin/sponge init` - Create file sponge.config.js.
 - `node node_modules/.bin/sponge sass` - Compile the css file.
 - `node node_modules/.bin/sponge sass:watch` - Compile the css file on every changes of sass file.
 - `node node_modules/.bin/sponge scripts` - Compile the js file.
 - `node node_modules/.bin/sponge scripts:watch` - Compile the js file on every changes of source js file.
 - `node node_modules/.bin/sponge build:views` - Apply gulp-useref plugin and minify css and js files.
 - `node node_modules/.bin/sponge build:images` - Optimize the file size of image and move the output file in 'build_images destination' provided of config file.
 - `node node_modules/.bin/sponge build:fonts` - Just move the fonts in `build_fonts destination` provided of config file.

 - `node node_modules/.bin/sponge watch` - Run commands `node node_modules/.bin/sponge sass:watch` and `node node_modules/.bin/sponge scripts:watch`.
 - `node node_modules/.bin/sponge delete:dist` - Remove dist folder.
 - `node node_modules/.bin/sponge build:dist` - Run commands `node node_modules/.bin/sponge delete:dist`, `node node_modules/.bin/sponge sass`, `node node_modules/.bin/sponge scripts`, `node node_modules/.bin/sponge build:views`, `node node_modules/.bin/sponge build:images` and `node node_modules/.bin/sponge build:fonts`.

## Suggestion

I suggest to install `npx` node package in your local machine to execute the command simple.

Instead of for example `node node_modules/.bin/sponge watch`, you can achieve that with this very simple command `npx sponge watch`. Pretty cool ha!

## Features

 - Sponge Rod used `autoprefixer`, `csscomb` and `merge media query` plugins after compile the sass file into css file.
 - It used webpack comes from `webpack-stream` plugin.
 - It decrease the file size of images without change the quality.

## Sponge Rod configuration schema.

### sass

##### Required
 - `src`[array|string] - Source files to be compiled.
 - `dest`[string] - Output destination.

##### Optional
 - `callback`[function] - This will be trigger after the sass command execute.
 - `options`[json] - The option of `gulp.src` function.
 - `command`[string] - It would override the `sass` command.
 - `watch_command`[string] - It would override the `sass:watch` command.
 - `notify`[boolean] - Show toast message after sass command task completed.

### scripts

##### Required
 - `entries`[json] - Key is to be filename and the source file to be compiled.
 - `dest`[string] - Output destination.

##### Optional
 - `callback`[function] - This will be trigger after the scripts command execute.
 - `options`[json] - The option of `gulp.src` function.
 - `command`[string] - It would override the `scripts` command.
 - `watch_command`[string] - It would override the `scripts:watch` command.
 - `notify`[boolean] - Show toast message after scripts command task completed.

### build_views

##### Required
 - `src`[array|string] - Source files to be compiled.
 - `dest`[string] - Output destination.

##### Optional
 - `callback`[function] - This will be trigger after the build:views command execute.
 - `options`[json] - The option of `gulp.src` function.
 - `command`[string] - It would override the `build:views` command.
 - `notify`[boolean] - Show toast message after build:views command task completed.

### build_images

##### Required
 - `src`[array|string] - Source files to be compiled.
 - `dest`[string] - Output destination.

##### Optional
 - `callback`[function] - This will be trigger after the build:images command execute.
 - `options`[json] - The option of `gulp.src` function.
 - `command`[string] - It would override the `build:images` command.
 - `use_flatten`[boolean] - Make the output files flatten.
 - `notify`[boolean] - Show toast message after build:images command task completed.

### build_fonts

##### Required
 - `src`[array|string] - Source files to be compiled.
 - `dest`[string] - Output destination.

##### Optional
 - `callback`[function] - This will be trigger after the build:fonts command execute.
 - `options`[json] - The option of `gulp.src` function.
 - `command`[string] - It would override the `build:fonts` command.
 - `use_flatten`[boolean] - Make the output files flatten.
 - `notify`[boolean] - Show toast message after build:fonts command task completed.

### build

##### Optional
 - `callback`[function] - To be call after build task completed.
 - `notify`[boolean] - Show toast message after build command task completed.

### unbuild

##### Optional
 - `dir`[string|glob] - Files or directory to be delete.
 - `callback`[function] - To be call after unbuild task completed.
 - `notify`[boolean] - Show toast message after unbuild command task completed.

## Example

Checkout the [example](https://github.com/rodrigoiii/sponge-rod/blob/master/app) and test it in your local machine.

## LICENSE
Sponge Rod is released under the MIT Licence.