var Helper = {
  scrollMessage: function(height, callback) {
    var bottom = height || $('.messages').prop('scrollHeight');
    $('.messages').animate({ scrollTop: bottom }, "fast", callback);
  }
};

module.exports = Helper;
