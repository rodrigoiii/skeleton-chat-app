var _ = require("underscore");

module.exports = function(gulp, plugins, config, commands) {
  return function() {
    var base = "";
    if (typeof config.scripts.options !== "undefined") {
      if (typeof config.scripts.options.base !== "undefined") {
        base = config.scripts.options.base;
      }
    }

    if (!_.isEmpty(base)) {
      gulp.watch(base.replace(/\/$/, "") + "/**/*.js", [commands.scripts]);
    } else {
      var to_be_watch = [];

      if (!_.isEmpty(base)) {
        base = base.replace(/\/$/, "") + "/";
      }

      if (typeof config.scripts.entries_object !== "undefined") {
        to_be_watch = _.map(_.values(config.scripts.entries_object), function(entry) {
          return "./" + base + entry;
        });
      }

      if (typeof config.scripts.entries_array !== "undefined") {
        to_be_watch = _.union(to_be_watch, _.map(config.scripts.entries_array, function(entry) {
          return "./" + base + entry + ".js";
        }));
      }

      gulp.watch(to_be_watch, [commands.scripts]);
    }
  };
};
