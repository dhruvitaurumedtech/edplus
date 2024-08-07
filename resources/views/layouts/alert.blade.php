<script>
    window.setTimeout(function() {
        $(".alert-success").slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);
</script>
<div class="row">
    <div class="col-md-10 offset-md-1">
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
    </div>
    <div class="col-md-10 offset-md-1">
        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
    </div>
</div>