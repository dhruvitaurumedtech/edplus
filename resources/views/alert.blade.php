<div class="row">
    <div class="col-md-10 offset-md-1">
      @if (session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
      @endif
    </div>
  </div>

  <script>
    window.setTimeout(function() {
      $(".alert-success").slideUp(500, function() {
        $(this).remove();
      });
    }, 3000);
  </script>