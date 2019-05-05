var _ = require("underscore"),
  notifier = require("node-notifier");

module.exports = function(gulp, plugins, config) {
  return function() {
    var options = (typeof config.sass.options !== config.sass.options) ? config.sass.options : {};
    var notify = typeof config.sass.notify !== "undefined" ? config.sass.notify : true;

    return gulp.src(config.sass.src, _.extend({ allowEmpty: true }, options))
      .pipe(plugins.plumber({
        errorHandler: function(err) {
          console.log(err);
          this.emit("end");
        }
      }))
      .pipe(plugins.sass())
      .pipe(plugins.autoprefixer())
      .pipe(plugins.csscomb())
      .pipe(plugins.mmq({
        log: true
      }))
      .pipe(gulp.dest(config.sass.dest))
      .on('end', function() {
        if (typeof config.sass.callback !== "undefined") {
          config.sass.callback();
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Compile sass files completed!"
          });
        }
      });
  };
};
