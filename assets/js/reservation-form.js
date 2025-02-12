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
