<?php
if (!defined('ABSPATH')) exit;

$reservation_days = get_option('wp_reservation_days', []);
$time_slots = explode(',', get_option('wp_reservation_time_slots', ''));
?>

<div id="wp-reservation-form-container">
    <h2>予約フォーム</h2>
    <div id="wp-reservation-calendar">
        <!-- カレンダーの表示 -->
        <!-- ...existing code... -->
    </div>
    <div id="wp-reservation-time-slots" style="display:none;">
        <button id="back-to-calendar" class="button">カレンダーに戻る</button>
        <!-- 時間帯の表示 -->
        <!-- ...existing code... -->
    </div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const calendar = document.getElementById('wp-reservation-calendar');
        const timeSlots = document.getElementById('wp-reservation-time-slots');
        const backToCalendarButton = document.getElementById('back-to-calendar');

        // 時間帯を表示する関数
        function showTimeSlots() {
            calendar.style.display = 'none';
            timeSlots.style.display = 'block';
        }

        // カレンダーに戻る関数
        function showCalendar() {
            timeSlots.style.display = 'none';
            calendar.style.display = 'block';
        }

        // 戻るボタンのイベントリスナー
        backToCalendarButton.addEventListener('click', function () {
            showCalendar();
        });

        // カレンダーの日付クリックイベント
        document.querySelectorAll('.calendar-cell.clickable').forEach(cell => {
            cell.addEventListener('click', function () {
                showTimeSlots();
            });
        });
    });
</script>
