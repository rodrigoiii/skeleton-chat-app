var runSequence = require("run-sequence"),
  notifier = require("node-notifier");

module.exports = function(gulp, plugins, config, commands) {
  return function() {
    var notify = false;
    if (typeof config.build !== "undefined") {
      if (typeof config.build.notify !== "undefined") {
        notify = config.build.notify;
      }
    }

    runSequence(commands.unbuild,
      commands.sass,
      commands.scripts,
      [
        commands.build_views,
        commands.build_images,
        commands.build_fonts
      ],
      function() {
        if (typeof config.build !== "undefined") {
          if (typeof config.build.callback !== "undefined") {
            config.build.callback();
          }
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Build all files completed!"
          });
        }
      }
    );
  };
};
