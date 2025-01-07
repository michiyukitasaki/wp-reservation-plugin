<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
$time_slots = explode(',', get_option('wp_reservation_time_slots', ''));

?>

<div id="wp-reservation-form-container">
    <h2>Make a Reservation</h2>
    <div id="wp-reservation-calendar"></div>
    <div id="wp-reservation-form" style="display:none;">
        <form id="reservationForm">
            <input type="hidden" id="reservationDate" name="reservation_date" required>
            <input type="hidden" id="reservationTimeSlot" name="time_slot" required>

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>

            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" placeholder="Your Phone Number" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Your Email Address" required>

            <label for="notes">Notes:</label>
            <textarea id="notes" name="notes" placeholder="Additional Notes"></textarea>

            <button type="submit" class="button">Reserve</button>
        </form>
    </div>
</div>

<script>
    const reservationDays = <?php echo json_encode(get_option('wp_reservation_days', [])); ?>;
    const timeSlots = <?php echo json_encode(explode(',', get_option('wp_reservation_time_slots', ''))); ?>;
</script>
