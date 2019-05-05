var gulp = require("gulp"),
    config = require(process.cwd() + "/sponge.config"),
    plugins = require("gulp-load-plugins")({
        DEBUG: config.debug,
        rename: {
            'gulp-merge-media-queries': "mmq"
        }
    });

var commands = {
    sass: typeof config.sass.command !== "undefined" ? config.sass.command : "sass",
    sass_watch: typeof config.sass.watch_command !== "undefined" ? config.sass.watch_command : "sass:watch",
    scripts: typeof config.scripts.command !== "undefined" ? config.scripts.command : "scripts",
    scripts_watch: typeof config.scripts.watch_command !== "undefined" ? config.scripts.watch_command : "scripts:watch",

    build_views: typeof config.build_views.command !== "undefined" ? config.build_views.command : "build:views",
    build_images: typeof config.build_images.command !== "undefined" ? config.build_images.command : "build:images",
    build_fonts: typeof config.build_fonts.command !== "undefined" ? config.build_fonts.command : "build:fonts",
    build: "build",
    unbuild: "unbuild"
};

function getTask(task)
{
    return require(__dirname + "/tasks/" + task)(gulp, plugins, config, commands);
}

gulp.task(commands.sass, getTask("sass"));
gulp.task(commands.scripts, getTask("scripts"));
gulp.task(commands.sass_watch, [commands.sass], getTask('sass-watch'));
gulp.task(commands.scripts_watch, [commands.scripts], getTask('scripts-watch'));
gulp.task("watch", [commands.sass_watch, commands.scripts_watch], function () {});

gulp.task(commands.build_views, getTask("build-views"));
gulp.task(commands.build_images, getTask("build-images"));
gulp.task(commands.build_fonts, getTask("build-fonts"));
gulp.task(commands.build, getTask("build"));
gulp.task(commands.unbuild, getTask("unbuild"));

module.exports = {
    commands: commands,

    runGulpCommand: function(command) {
        gulp.start(command);
    }
};