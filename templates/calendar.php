<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
?>

<div id="wp-reservation-calendar-container">
    <h2>Select a Date</h2>
    <div id="wp-reservation-calendar"></div>
</div>
<script>
    const reservationDays = <?php echo json_encode($reservation_days); ?>;
</script>
