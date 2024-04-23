
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

  //search code 
  $(document).ready(function () {
    $(".myInput").on("keyup", function () {
      var value = $(this).val().toLowerCase();
      $(".myTable tr:not('.no-records')").filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
      var trSel = $(".myTable tr:not('.no-records'):visible")
      // Check for number of rows & append no records found row
      if (trSel.length == 0) {
        $(".myTable").html('<tr class="no-records"><td colspan="3">No record found.</td></tr>')
      }
      else {
        $('.no-records').remove()
      }

    });
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


// sidebar 
const mobileScreen = window.matchMedia("(max-width: 990px )");
$(document).ready(function () {
  $(".dashboard-nav-dropdown-toggle").click(function () {
    $(this).closest(".dashboard-nav-dropdown")
      .toggleClass("show")
      .find(".dashboard-nav-dropdown")
      .removeClass("show");
    $(this).parent()
      .siblings()
      .removeClass("show");
  });
  $(".menu-toggle").click(function () {
    if (mobileScreen.matches) {
      $(".dashboard-nav").toggleClass("mobile-show");
    } else {
      $(".dashboard").toggleClass("dashboard-compact");
    }
  });
});


// Profile icons js 
function toggleDropdown() {
  var dropdownMenu = document.getElementById("pro-icon-dropdownMenu");
  var expanded = dropdownMenu.getAttribute("aria-expanded");

  if (expanded === "true") {
    dropdownMenu.style.display = "none";
    dropdownMenu.setAttribute("aria-expanded", "false");
  } else {
    dropdownMenu.style.display = "block";
    dropdownMenu.setAttribute("aria-expanded", "true");
  }
}


(function ($) {
  var CheckboxDropdown = function (el) {
    var _this = this;
    this.isOpen = false;
    this.areAllChecked = false;
    this.$el = $(el);
    this.$label = this.$el.find('.dropdown-label');
    this.$checkAll = this.$el.find('[data-toggle="check-all"]').first();
    this.$inputs = this.$el.find('[type="checkbox"]');

    this.onCheckBox();

    this.$label.on('click', function (e) {
      e.preventDefault();
      _this.toggleOpen();
    });

    this.$checkAll.on('click', function (e) {
      e.preventDefault();
      _this.onCheckAll();
    });

    this.$inputs.on('change', function (e) {
      _this.onCheckBox();
    });
  };

  CheckboxDropdown.prototype.onCheckBox = function () {
    this.updateStatus();
  };

  CheckboxDropdown.prototype.updateStatus = function () {
    var checked = this.$el.find(':checked');

    this.areAllChecked = false;
    this.$checkAll.html('Check All');

    if (checked.length <= 0) {
      this.$label.html('Select Options');
    } else if (checked.length === 1) {
      this.$label.html(checked.parent('label').text());
    } else if (checked.length === this.$inputs.length) {
      this.$label.html('All Selected');
      this.areAllChecked = true;
      this.$checkAll.html('Uncheck All');
    } else {
      this.$label.html(checked.length + ' Selected');
    }
  };

  CheckboxDropdown.prototype.onCheckAll = function (checkAll) {
    if (!this.areAllChecked || checkAll) {
      this.areAllChecked = true;
      this.$checkAll.html('Uncheck All');
      this.$inputs.prop('checked', true);
    } else {
      this.areAllChecked = false;
      this.$checkAll.html('Check All');
      this.$inputs.prop('checked', false);
    }

    this.updateStatus();
  };

  CheckboxDropdown.prototype.toggleOpen = function (forceOpen) {
    var _this = this;

    if (!this.isOpen || forceOpen) {
      this.isOpen = true;
      this.$el.addClass('on');
      $(document).on('click', function (e) {
        if (!$(e.target).closest('[data-control]').length) {
          _this.toggleOpen();
        }
      });
    } else {
      this.isOpen = false;
      this.$el.removeClass('on');
      $(document).off('click');
    }
  };

  var checkboxesDropdowns = document.querySelectorAll('[data-control="checkbox-dropdown"]');
  for (var i = 0, length = checkboxesDropdowns.length; i < length; i++) {
    new CheckboxDropdown(checkboxesDropdowns[i]);
  }
})(jQuery);
