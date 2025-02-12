<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
$time_slots = explode(',', get_option('wp_reservation_time_slots', ''));
?>

<div id="wp-reservation-form-container">
    <a href="/" id="back-to-home" class="back-to-home-link"><i class="fas fa-home"></i></a>
    <div id="wp-reservation-calendar">
        <!-- カレンダーの表示 -->
        <!-- ...existing code... -->
    </div>
    <div id="wp-reservation-time-slots" style="display:none;">
        <!-- 時間帯の表示 -->
        <!-- ...existing code... -->
    </div>
    <div id="wp-reservation-form" style="display:none;">
        <form id="reservationForm">
            <input type="hidden" id="reservationDate" name="reservation_date" required>
            <input type="hidden" id="reservationTimeSlot" name="time_slot" required>

            <label for="name"><i class="fas fa-user"></i> 名前：</label>
            <input type="text" id="name" name="name" placeholder="お名前" required>

            <label for="phone"><i class="fas fa-phone"></i> 電話番号：</label>
            <input type="tel" id="phone" name="phone" placeholder="電話番号" required>

            <label for="email"><i class="fas fa-envelope"></i> メールアドレス：</label>
            <input type="email" id="email" name="email" placeholder="メールアドレス" required>

            <label for="notes"><i class="fas fa-sticky-note"></i> 備考：</label>
            <textarea id="notes" name="notes" placeholder="備考"></textarea>

            <button type="submit" class="button">予約</button>
        </form>
    </div>
</div>

<?php
wp_enqueue_script('wp-reservation-form-script', plugin_dir_url(__FILE__) . '../assets/js/reservation-form.js', [], false, true);
?>
