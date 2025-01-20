<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
$time_slots = explode(',', get_option('wp_reservation_time_slots', ''));

?>

<div id="wp-reservation-form-container">
    <h2>予約をする</h2>
    <div id="wp-reservation-calendar"></div>
    <div id="wp-reservation-form" style="display:none;">
        <form id="reservationForm">
            <input type="hidden" id="reservationDate" name="reservation_date" required>
            <input type="hidden" id="reservationTimeSlot" name="time_slot" required>

            <label for="name">名前：</label>
            <input type="text" id="name" name="name" placeholder="お名前" required>

            <label for="phone">電話番号：</label>
            <input type="tel" id="phone" name="phone" placeholder="電話番号" required>

            <label for="email">メールアドレス：</label>
            <input type="email" id="email" name="email" placeholder="メールアドレス" required>

            <label for="notes">備考：</label>
            <textarea id="notes" name="notes" placeholder="備考"></textarea>

            <button type="submit" class="button">予約する</button>
        </form>
    </div>
</div>

<script>
    const reservationDays = <?php echo json_encode(get_option('wp_reservation_days', [])); ?>;
    const timeSlots = <?php echo json_encode(explode(',', get_option('wp_reservation_time_slots', ''))); ?>;
</script>
