var del = require("del"),
  notifier = require("node-notifier");

var RED = "\x1b[31m%s\x1b[0m";

module.exports = function(gulp, plugins, config) {
  return function() {
    var notify = typeof config.unbuild.notify !== "undefined" ? config.unbuild.notify : false;

    del(config.unbuild.dir).then(paths => {
      if (paths.length > 0) {
        console.log(RED, paths.join("\n") + " deleted");

        if (typeof config.unbuild !== "undefined") {
          if (typeof config.unbuild.callback !== "undefined") {
            config.unbuild.callback();
          }
        }

        if (notify) {
          notifier.notify({
            title: "Sponge Rod",
            message: "Unbuild files completed!"
          });
        }
      }
    });
  };
};
