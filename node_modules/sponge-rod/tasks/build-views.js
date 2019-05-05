var notifier = require("node-notifier");

module.exports = function(gulp, plugins, config) {
  return function() {
    var options = (typeof config.build_views.options !== config.build_views.options) ? config.build_views.options : {};

    var notify = typeof config.build_views.notify !== "undefined" ? config.build_views.notify : false;

    return gulp.src(config.build_views.src, options)
      .pipe(plugins.useref(config.build_views.useref_options))
      .pipe(plugins.if('*.js', plugins.uglify()))
      .pipe(plugins.if('*.css', plugins.cssnano()))
      .pipe(gulp.dest(config.build_views.dest))
      .on('end', function() {
        if (typeof config.build_views.callback !== "undefined") {
          config.build_views.callback();
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Build view files completed!"
          });
        }
      });
  };
};
