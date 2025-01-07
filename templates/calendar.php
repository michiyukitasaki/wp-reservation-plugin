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
<style>
    #wp-reservation-calendar-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    #wp-reservation-calendar {
        width: 100%;
        height: 400px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
