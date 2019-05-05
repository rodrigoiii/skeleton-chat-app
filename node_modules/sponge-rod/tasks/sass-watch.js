module.exports = function(gulp, plugins, config, commands) {
  return function() {
    gulp.watch(config.sass.src, [commands.sass]);
  };
};
