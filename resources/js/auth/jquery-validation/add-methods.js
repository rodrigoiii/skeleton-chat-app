$.validator.addMethod("regex", function(value, element, regexp) {
  var re = new RegExp(regexp);
  return this.optional(element) || re.test(value);
}, "Please check your input.");

$.validator.addMethod("password_strength", function(value, element, params) {
  var min_length = params.min_length || 8;
  var lower = params.lower || 0;
  var upper = params.upper || 0;
  var number = params.number || 0;
  var special_char = params.special_char || 0;

  return this.optional(element) || (
    value.length >= min_length && // password length
    (value.match(/[a-z]/g) !== null ? value.match(/[a-z]/g).length : 0) >= lower && // lower case
    (value.match(/[A-Z]/g) !== null ? value.match(/[A-Z]/g).length : 0) >= upper && // upper case
    (value.match(/[0-9]/g) !== null ? value.match(/[0-9]/g).length : 0) >= number && // number
    (value.match(/[^a-zA-Z0-9\s]/g) !== null ? value.match(/[^a-zA-Z0-9\s]/g).length : 0) >= special_char // special characters
  );
}, function(params, element) {
  var min_length = params.min_length || 8;
  var lower = params.lower || 0;
  var upper = params.upper || 0;
  var number = params.number || 0;
  var special_char = params.special_char || 0;

  var value = $(element).val();
  var error_message = false;

  if (value.length < min_length) {
    error_message = "Password must be at least " + min_length + " character(s).";
  } else if ((value.match(/[a-z]/g) !== null ? value.match(/[a-z]/g).length : 0) < lower) {
    error_message = "Password must have " + lower + " lower case.";
  } else if ((value.match(/[A-Z]/g) !== null ? value.match(/[A-Z]/g).length : 0) < upper) {
    error_message = "Password must have " + upper + " upper case.";
  } else if ((value.match(/[0-9]/g) !== null ? value.match(/[0-9]/g).length : 0) < number) {
    error_message = "Password must have " + number + " number(s).";
  } else if ((value.match(/[^a-zA-Z0-9\s]/g) !== null ? value.match(/[^a-zA-Z0-9\s]/g).length : 0) < special_char) {
    error_message = "Password must have " + special_char + " special character(s).";
  }

  return error_message;
});

$.validator.addMethod("file_size", function(value, element, params) {
  var KB = 1000;
  var MB = KB * 1000;

  var min_size = params.min_size || 0; // 0 default
  var max_size = params.max_size || MB * 5; // 5mb default

  var files = element.files;
  var is_size_ok;

  if (files.length === 1) { // single file
    is_size_ok = files[0].size >= min_size && files[0].size <= max_size;
  } else { // multiple
    is_size_ok = true;
    for (var i in files) {
      if (files[i].size < min_size || files[i].size > max_size) {
        is_size_ok = false;
        break;
      }
    }
  }

  return is_size_ok;
}, function(params, element) {
  var KB = 1000;
  var MB = KB * 1000;

  var min_size = params.min_size || KB; // 1kb default
  var max_size = params.max_size || MB * 5; // 5mb default

  var files = element.files;
  var error_message = false;

  if (files.length === 1) { // single file
    if (files[0].size < min_size) {
      error_message = "File size is too small.";
    } else if (files[0].size > max_size) {
      error_message = "File size is too large.";
    }
  } else { // multiple
    for (var i in files) {
      if (files[i].size < min_size || files[i].size > max_size) {
        if (files[i].size < min_size) {
          error_message += files[i].name + " file size is too small.<br />";
        } else if (files[i].size > max_size) {
          error_message += files[i].name + " file size is too large.<br />";
        }
      }
    }
  }

  return error_message;
});
