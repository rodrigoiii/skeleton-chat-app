function quickLoadImage(input, output_el) {
  var reader = new FileReader();

  reader.onload = function(e) {
    output_el.setAttribute('src', e.target.result);
  };

  if (typeof(input.files) !== "undefined") {
    if (input.files.length < 1) {
      output_el.setAttribute('src', "");
    } else if (input.files.length === 1) {
      if (/^image\/./g.test(input.files[0].type)) { // valid image
        reader.readAsDataURL(input.files[0]);
      } else {
        output_el.setAttribute('src', "");
        console.error("Invalid file type! It must be image.");
      }
    } else {
      output_el.setAttribute('src', "");
      console.error("File must be one only.");
    }
  }
}

function quickLoadImages(input, output_el) {
  var images_container = output_el[0].closest('#quick-load-images-container');

  // create div element if not exist
  if (images_container === null) {
    var div = document.createElement("div");
    div.setAttribute('id', "quick-load-images-container");

    // clone one preview element
    var clone_output_el = output_el[0].cloneNode(true);
    clone_output_el.style.display = "none";

    // summon the div element after the output element
    output_el[0].after(div);

    // put the clone output element inside of div container
    div.appendChild(clone_output_el);

    // remove output element
    output_el[0].remove();
  } else {
    // refresh image files
    var images = images_container.children;
    var images_length = images.length;
    for (var j = 1; j < images_length; j++) {
      images[1].remove(); // represent as array shift
    }
  }

  var files = input.files;

  if (typeof(files) !== "undefined") {
    if (files.length < 1) {
      document.getElementById('quick-load-images-container').innerHTML = "";
    } else {
      for (var k = 0; k < files.length; k++) {
        var reader = new FileReader();

        if (/^image\/./g.test(files[k].type)) { // valid image
          reader.readAsDataURL(files[k]);
        } else {
          console.error(files[k].name + " must be image.");
        }

        reader.onload = handleOnLoadReader;
      }
    }
  }
}

function handleOnLoadReader(e) {
  var images_container = document.getElementById('quick-load-images-container');
  var output_el = images_container.children[0].cloneNode(true);
  output_el.style.display = "block";

  var img = output_el.cloneNode(true);
  img.src = e.target.result;

  images_container.append(img);
}

window.quickLoadImage = quickLoadImage;
window.quickLoadImages = quickLoadImages;
window.handleOnLoadReader = handleOnLoadReader;
