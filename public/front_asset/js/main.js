
// choose file start
function readURL(input, imgControlName) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $(imgControlName).attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}

$("#imag").change(function () {
  // add your logic to decide which image control you'll use
  var imgControlName = "#ImgPreview";
  readURL(this, imgControlName);
  $('.preview1').addClass('it');
  $('.btn-rmv1').addClass('rmv');
});

$("#removeImage1").click(function (e) {
  e.preventDefault();
  $("#imag").val("");
  $("#ImgPreview").attr("src", "");
  $('.preview1').removeClass('it');
  $('.btn-rmv1').removeClass('rmv');
});
// choose file end



// Add more subject start 
$(document).ready(function () {
  $('#addTagBtn').click(function () {
    addTag();
  });

  $('#newTag').keypress(function (event) {
    if (event.which === 13) { // Check if Enter key is pressed
      addTag();
    }
  });

  // Function to add a new tag
  function addTag() {
    var newTagValue = $('#newTag').val().trim();
    if (newTagValue !== '') {
      var tag = $('<li>' + newTagValue + '<span class="remove-tag">&times;</span></li>');
      tag.find('.remove-tag').click(function () {
        $(this).parent().remove();
      });
      $('#tagList').append(tag);
      $('#newTag').val('');
    }
  }
});

// Add more subject end


// Reset Password start 
$(document).ready(function () {
  $("[id^='show_hide_password'] a").on('click', function (event) {
    event.preventDefault();
    var id = $(this).closest('.input-group').attr('id'); // Get the ID of the parent input group
    var input = $('#' + id + ' input'); // Find the input element within the parent input group
    var icon = $('#' + id + ' i'); // Find the icon element within the parent input group
    if (input.attr("type") == "text") {
      input.attr('type', 'password');
      icon.addClass("fa-eye-slash").removeClass("fa-eye");
    } else if (input.attr("type") == "password") {
      input.attr('type', 'text');
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });
});
// Reset Password end






