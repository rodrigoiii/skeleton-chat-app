var compiler = require("webpack"),
  webpack_stream = require("webpack-stream"),
  _ = require("underscore"),
  notifier = require("node-notifier");

module.exports = function(gulp, plugins, config) {
  return function() {
    var base = "";
    var options = {};
    if (typeof config.scripts.options !== "undefined") {
      options = config.scripts.options;

      if (typeof options.base !== "undefined") {
        base = options.base;
      }
    }

    var notify = (typeof config.scripts.notify !== "undefined") ? config.scripts.notify : true;

    return gulp.src("dummy-entry.js", _.extend({ allowEmpty: true }, options))
      .pipe(plugins.plumber({
        errorHandler: function(err) {
          console.log(err);
          this.emit("end");
        }
      }))
      .pipe(webpack_stream({
        entry: _.object(
          function() {
            var keys = [];

            if (typeof config.scripts.entries_object !== "undefined") {
              keys = _.keys(config.scripts.entries_object);
            }

            if (typeof config.scripts.entries_array !== "undefined") {
              keys = _.union(keys, config.scripts.entries_array);
            }

            return keys;
          }(),
          function() {
            var values = [];

            if (!_.isEmpty(base)) {
              base = base.replace(/\/$/, "") + "/";
            }

            if (typeof config.scripts.entries_object !== "undefined") {
              values = _.map(_.values(config.scripts.entries_object), function(entry) {
                return "./" + base + entry;
              });
            }

            if (typeof config.scripts.entries_array !== "undefined") {
              values = _.union(values, _.map(config.scripts.entries_array, function(entry) {
                return "./" + base + entry + ".js";
              }));
            }

            return values;
          }()
        ),

        output: {
          filename: "[name].js"
        }
      }, compiler))
      .pipe(gulp.dest(config.scripts.dest))
      .on('end', function() {
        if (typeof config.scripts.callback !== "undefined") {
          config.scripts.callback();
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Compile js files completed!"
          });
        }
      });
  };
};
