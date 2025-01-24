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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendar = document.getElementById('wp-reservation-calendar');
        const timeSlots = document.getElementById('wp-reservation-time-slots');
        const reservationForm = document.getElementById('wp-reservation-form');
        const form = document.getElementById('reservationForm');

        // 時間帯を表示する関数
        function showTimeSlots() {
            calendar.style.display = 'none';
            timeSlots.style.display = 'block';
            reservationForm.style.display = 'none';
        }

        // カレンダーに戻る関数
        function showCalendar() {
            timeSlots.style.display = 'none';
            calendar.style.display = 'block';
            reservationForm.style.display = 'none';
        }

        // 予約フォームに戻る関数
        function showReservationForm() {
            timeSlots.style.display = 'none';
            calendar.style.display = 'none';
            reservationForm.style.display = 'block';
        }

        // カレンダーの日付クリックイベント
        document.querySelectorAll('.calendar-cell.clickable').forEach(cell => {
            cell.addEventListener('click', function () {
                showTimeSlots();
            });
        });

        // 時間帯のボタンクリックイベント
        document.querySelectorAll('.slot-list button').forEach(button => {
            button.addEventListener('click', function () {
                showReservationForm();
            });
        });

        // フォーム送信後にフォームに戻る
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            // フォームデータを送信する処理を追加
            const formData = new FormData(form);
            fetch('/wp-json/wp-reservation/v1/reserve', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('予約が完了しました。');
                    form.reset();
                    showCalendar();
                } else {
                    alert('予約に失敗しました。');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('予約に失敗しました。');
            });
        });
    });
</script>
