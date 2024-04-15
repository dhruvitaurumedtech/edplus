<?php
$currentMonth = date('n'); // Get the current month (numeric, without leading zeros)
$currentYear = date('Y');   // Get the current year

// Determine the academic year
if ($currentMonth >= 9) { // If the current month is September or later
    $academicYear = $currentYear . '-' . ($currentYear + 1);
} else { // If the current month is before September
    $academicYear = ($currentYear - 1) . '-' . $currentYear;
}

?>
<footer class="footer">
    <div class="footer-text">
        <p>All rights reserved Copyright &#169; <?php echo $academicYear ?> Design &amp; Developed By <a href="javascript:void(0)">Aurum Edtech</a> </p>
    </div>
</footer>


<!-- js -->
<script src="{{asset('mayal_assets/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('mayal_assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('mayal_assets/js/main.js')}}"></script>