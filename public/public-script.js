document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('wp-reservation-calendar');
    const form = document.getElementById('reservationForm');
    const formContainer = document.getElementById('wp-reservation-form');

    const reservationDateInput = document.getElementById('reservationDate');
    const reservationTimeSlotInput = document.getElementById('reservationTimeSlot');

    // Generate calendar
    const today = new Date();
    const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

    for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(today.getFullYear(), today.getMonth(), i);
        const dayName = date.toLocaleDateString('en-US', { weekday: 'long' });

        const cell = document.createElement('div');
        cell.textContent = i;
        cell.dataset.date = date.toISOString().split('T')[0];

        if (reservationDays.includes(dayName)) {
            cell.classList.add('clickable');
            cell.addEventListener('click', () => {
                reservationDateInput.value = cell.dataset.date;
                showTimeSlots(cell.dataset.date);
            });
        } else {
            cell.style.backgroundColor = '#ccc';
            cell.style.cursor = 'not-allowed';
        }

        calendar.appendChild(cell);
    }

    function showTimeSlots(date) {
        const timeSlotContainer = document.createElement('div');
        timeSlots.forEach((slot) => {
            const slotButton = document.createElement('button');
            slotButton.textContent = slot;
            slotButton.addEventListener('click', () => {
                reservationTimeSlotInput.value = slot;
                formContainer.style.display = 'block';
            });
            timeSlotContainer.appendChild(slotButton);
        });
        calendar.innerHTML = '';
        calendar.appendChild(timeSlotContainer);
    }

    // Handle form submission
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('/wp-json/wp-reservation/v1/reserve', {
            method: 'POST',
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert('Reservation successful!');
                form.reset();
                formContainer.style.display = 'none';
            } else {
                alert(data.message || 'Failed to make reservation.');
            }
        })
        .catch(() => {
            alert('Error processing reservation.');
        });
    });
});
