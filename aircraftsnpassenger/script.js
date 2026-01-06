// (Keep your existing calendar generation logic here)

document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.getElementById('single-calendar-display-container');
    const displayedMonthTitle = document.getElementById('displayed-month-title');
    const navButtons = document.querySelectorAll('.nav-button');

    let currentYear = 2025; // Or whatever your initial year is
    let currentMonthIndex = 5; // June (0-indexed)

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    function renderCalendar(year, monthIndex) {
        calendarContainer.innerHTML = ''; // Clear previous calendar
        displayedMonthTitle.textContent = `${monthNames[monthIndex]} ${year} Schedule`;

        const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
        const firstDayOfMonth = new Date(year, monthIndex, 1).getDay(); // 0 for Sunday, 1 for Monday, etc.

        const calendarGrid = document.createElement('div');
        calendarGrid.classList.add('calendar-grid');

        // Add day names
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayNames.forEach(dayName => {
            const dayNameCell = document.createElement('div');
            dayNameCell.classList.add('calendar-day-name');
            dayNameCell.textContent = dayName;
            calendarGrid.appendChild(dayNameCell);
        });

        // Add empty cells for days before the 1st
        for (let i = 0; i < firstDayOfMonth; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('calendar-day', 'empty');
            calendarGrid.appendChild(emptyCell);
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.classList.add('calendar-day');
            dayCell.textContent = day;

            // Add event listener to each day cell
            dayCell.addEventListener('click', () => {
                const formattedDate = `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                window.location.href = `aircraft-selection.html?date=${formattedDate}`;
            });

            calendarGrid.appendChild(dayCell);
        }
        calendarContainer.appendChild(calendarGrid);
    }

    // Initial render for June 2025
    renderCalendar(currentYear, currentMonthIndex);

    navButtons.forEach(button => {
        button.addEventListener('click', () => {
            currentYear = parseInt(button.dataset.year);
            currentMonthIndex = parseInt(button.dataset.monthIndex);
            renderCalendar(currentYear, currentMonthIndex);
        });
    });
});