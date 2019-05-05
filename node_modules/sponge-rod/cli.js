#!/usr/bin/env node
var VERSION = "3.10.1";

var fse = require('fs-extra'),
    program = require("commander");

if (!fse.existsSync(process.cwd() + "/sponge.config.js")) {
    // init config command
    program
    .command('init')
    .description("Create file sponge.config.js.")
    .action(function () {
        try {
            fse.copySync(__dirname + "/sponge.config.js.example", process.cwd() + "/sponge.config.js");
            console.log("Successfully created file sponge.config.js.");
        } catch (err) {
            console.error(err);
        }
    });

    program.parse(process.argv);
    process.exit(1);
}

var app = require("./sponge"),
    commands = app.commands;

// sass command
program
.command(commands.sass)
.description("Compile the css file.")
.action(function () {
    app.runGulpCommand(commands.sass);
});

// sass:watch command
program
.command(commands.sass_watch)
.description("Compile the css file on every changes of sass file.")
.action(function () {
    app.runGulpCommand(commands.sass_watch);
});

// scripts command
program
.command(commands.scripts)
.description("Compile the js file.")
.action(function () {
    app.runGulpCommand(commands.scripts);
});

// scripts:watch command
program
.command(commands.scripts_watch)
.description("Compile the js file on every changes of source js file.")
.action(function () {
    app.runGulpCommand(commands.scripts_watch);
});

// watch command
program
.command('watch')
.description("Run commands `"+commands.sass+"` and `"+commands.scripts+"`.")
.action(function () {
    app.runGulpCommand("watch");
});

// build:views command
program
.command(commands.build_views)
.description("Apply gulp-useref plugin and minify css and js files.")
.action(function () {
    app.runGulpCommand(commands.build_views);
});

// build:images command
program
.command(commands.build_images)
.description("Optimize the file size of image and move the output file in 'build_images destination' provided of config file.")
.action(function () {
    app.runGulpCommand(commands.build_images);
});

// build:fonts command
program
.command(commands.build_fonts)
.description("Just move the fonts in `build_fonts destination` provided of config file.")
.action(function () {
    app.runGulpCommand(commands.build_fonts);
});

// unbuild command
program
.command(commands.unbuild)
.description("Remove the directory 'unbuild_dir' provided of config file.")
.action(function () {
    app.runGulpCommand(commands.unbuild);
});

// build command
program
.command(commands.build)
.description("\n\tRun commands in this order: \n\t* `unbuild` \n\t* `sass` \n\t* `scripts` \n\t* `build:views`, `build:images` and * `build:fonts` in parallel.")
.action(function () {
    app.runGulpCommand(commands.build);
});

program
.version(VERSION, "-v, --version")
.arguments('<cmd>')
.action(function (cmd) {
    command_input = cmd;
});

program.parse(process.argv);

if (typeof command_input !== "undefined") {
    // display friendly error output if command is not exist
    if (!(command_input in commands)) {
        console.error(command_input + " is not a valid command. Use --help to display available commands.");
        process.exit(1);
    }
}
