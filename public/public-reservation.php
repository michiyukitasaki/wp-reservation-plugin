<?php
if (!defined('ABSPATH')) exit;

function wp_reservation_render_public_page() {
    $reservation_days = get_option('wp_reservation_days', []);
    $time_slots = explode(',', get_option('wp_reservation_time_slots', ''));
    ?>
    <div id="wp-reservation-container">
        <h2>Make a Reservation</h2>
        <div id="wp-reservation-calendar"></div>
        <div id="wp-reservation-form" style="display:none;">
            <h3>Reserve a Time Slot</h3>
            <form id="reservationForm">
                <input type="hidden" id="reservationDate" name="reservation_date">
                <input type="hidden" id="reservationTimeSlot" name="time_slot">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <label for="notes">Notes:</label>
                <textarea id="notes" name="notes"></textarea>
                <button type="submit" class="button">Reserve</button>
            </form>
        </div>
    </div>
    <script>
        const reservationDays = <?php echo json_encode($reservation_days); ?>;
        const timeSlots = <?php echo json_encode($time_slots); ?>;
    </script>
    <?php
}
add_shortcode('wp_reservation_public', 'wp_reservation_render_public_page');
?>
