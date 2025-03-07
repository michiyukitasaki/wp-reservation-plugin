document.addEventListener("DOMContentLoaded", () => {
    const calendarContainer = document.getElementById("wp-reservation-calendar");
    const timeSlotsContainer = document.getElementById("wp-reservation-time-slots");
    const formContainer = document.getElementById("wp-reservation-form");
    const form = document.getElementById("reservationForm");
    const reservationDateInput = document.getElementById("reservationDate");
    const reservationTimeSlotInput = document.getElementById("reservationTimeSlot");

    // 予約可能な曜日と時間帯を取得
    const reservationDays = easyresySettings.reservationDays || [];
    const timeSlots = easyresySettings.timeSlots || [];

    console.log('Reservation Days:', reservationDays); // デバッグ用
    console.log('Time Slots:', timeSlots); // デバッグ用

    // 日本語の曜日名を英語の曜日名に変換
    const dayMap = {
        "日曜日": "Sunday",
        "月曜日": "Monday",
        "火曜日": "Tuesday",
        "水曜日": "Wednesday",
        "木曜日": "Thursday",
        "金曜日": "Friday",
        "土曜日": "Saturday"
    };

    // 日本語の曜日名を短縮形に変換
    const shortDayMap = {
        "日曜日": "日",
        "月曜日": "月",
        "火曜日": "火",
        "水曜日": "水",
        "木曜日": "木",
        "金曜日": "金",
        "土曜日": "土"
    };

    // 今日の日付
    let currentDate = new Date();

    // カレンダーをレンダリング
    function renderCalendar(date) {
        calendarContainer.innerHTML = ""; // 既存のカレンダーをクリア

        const year = date.getFullYear();
        const month = date.getMonth();

        // 現在の年月を表示
        const header = document.createElement("div");
        header.classList.add("calendar-header");
        header.innerHTML = `
            <button id="prevMonth">&lt;</button>
            <h2>${date.toLocaleDateString("ja-JP", { year: "numeric", month: "long" })}</h2>
            <button id="nextMonth">&gt;</button>
        `;
        calendarContainer.appendChild(header);

        // 曜日を一行だけ表示
        const daysOfWeek = ["日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日"];
        const daysOfWeekRow = document.createElement("div");
        daysOfWeekRow.classList.add("calendar-days-of-week");
        daysOfWeek.forEach(day => {
            const dayElement = document.createElement("div");
            dayElement.classList.add("calendar-day-of-week");
            dayElement.textContent = shortDayMap[day];
            daysOfWeekRow.appendChild(dayElement);
        });
        calendarContainer.appendChild(daysOfWeekRow);

        // 日付部分を作成
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const daysContainer = document.createElement("div");
        daysContainer.classList.add("calendar-days");

        // 空白セルを追加
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement("div");
            emptyCell.classList.add("calendar-cell", "empty");
            daysContainer.appendChild(emptyCell);
        }

        // 日付セルを追加
        for (let i = 1; i <= daysInMonth; i++) {
            const cellDate = new Date(year, month, i);
            const dayName = cellDate.toLocaleDateString("ja-JP", { weekday: "long" });

            const cell = document.createElement("div");
            cell.textContent = i; // 日付のみ表示
            cell.dataset.date = cellDate.toLocaleDateString("ja-JP"); // データ属性に日付を保存
            cell.classList.add("calendar-cell");

            // アクティブな日付のみクリック可能
            if (reservationDays.includes(dayMap[dayName])) {
                cell.classList.add("clickable");
                cell.addEventListener("click", () => {
                    showTimeSlots(cell.dataset.date);
                });
            } else {
                cell.classList.add("disabled");
            }

            daysContainer.appendChild(cell);
        }

        calendarContainer.appendChild(daysContainer);

        // 前月・次月ボタンのイベントリスナーを登録
        document.getElementById("prevMonth").addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate);
        });
        document.getElementById("nextMonth").addEventListener("click", () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate);
        });
    }

    // 時間スロットを表示
    function showTimeSlots(date) {
        console.log('Clicked Date:', date); // デバッグ用
        reservationDateInput.value = date;

        const timeSlotContainer = document.createElement("div");
        timeSlotContainer.innerHTML = `
            <div class="time-slot-header">
                <h3>${date}の予約可能な時間帯</h3>
                <p>以下の時間帯から選択してください。</p>
            </div>
        `;

        if (timeSlots.length === 0) {
            timeSlotContainer.innerHTML += `<p>この日の予約可能な時間帯はありません。</p>`;
        } else {
            const slotList = document.createElement("div");
            slotList.classList.add("slot-list");
            timeSlots.forEach((slot) => {
                const slotButton = document.createElement("button");
                slotButton.textContent = slot;

                // 予約可能な残数を取得して表示
                fetch(`/wp-json/easyresy/v1/availability?date=${encodeURIComponent(date)}&time_slot=${encodeURIComponent(slot)}`)
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.createElement("span");
                        badge.classList.add("badge");
                        badge.textContent = `残り ${data.available}`;

                        slotButton.appendChild(badge);

                        // 残数によってボタンの色を変更
                        if (data.available >= 3) {
                            slotButton.classList.add("available");
                        } else if (data.available == 2) {
                            slotButton.classList.add("limited");
                        } else if (data.available == 1) {
                            slotButton.classList.add("few");
                        } else {
                            slotButton.classList.add("full");
                            slotButton.disabled = true;
                        }

                        if (data.available > 0) {
                            slotButton.addEventListener("click", () => {
                                // 他のボタンを非表示にする
                                const allButtons = slotList.querySelectorAll("button");
                                allButtons.forEach(btn => {
                                    if (btn !== slotButton) {
                                        btn.style.display = "none";
                                    }
                                });

                                reservationTimeSlotInput.value = slot;
                                formContainer.style.display = "block";
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        slotButton.textContent = 'Error';
                        slotButton.disabled = true;
                    });

                slotList.appendChild(slotButton);
            });
            timeSlotContainer.appendChild(slotList);
        }

        calendarContainer.style.display = 'none'; // カレンダーを非表示
        timeSlotsContainer.innerHTML = ""; // 時間スロットをクリア
        timeSlotsContainer.appendChild(timeSlotContainer); // 時間スロットを描画
        timeSlotsContainer.style.display = 'block'; // 時間スロットを表示
    }

    // 初期のカレンダーを表示
    renderCalendar(currentDate);
});
