var notifier = require("node-notifier");

module.exports = function(gulp, plugins, config) {
  return function() {
    var options = (typeof config.build_fonts.options !== config.build_fonts.options) ? config.build_fonts.options : {};

    var flatten = typeof config.build_fonts.use_flatten !== "undefined" ? config.build_fonts.use_flatten : false;
    var notify = typeof config.build_fonts.notify !== "undefined" ? config.build_fonts.notify : false;

    return gulp.src(config.build_fonts.src, options)
      .pipe(plugins.if(flatten, plugins.flatten()))
      .pipe(gulp.dest(config.build_fonts.dest))
      .on('end', function() {
        if (typeof config.build_fonts.callback !== "undefined") {
          config.build_fonts.callback();
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Build font files completed!"
          });
        }
      });
  };
};
