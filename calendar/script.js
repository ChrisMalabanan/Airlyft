document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const calendarContainer = document.getElementById('single-calendar-display-container');
    const monthTitle = document.getElementById('displayed-month-title');
    
    // Get current date (normalized to midnight)
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Calendar state
    let currentYear = today.getFullYear();
    let currentMonth = today.getMonth();

    // Initialize calendar
    function init() {
        renderCalendar(currentYear, currentMonth);
        setupMonthButtons();
        setupYearButtons();
    }

    // Main calendar rendering function
    function renderCalendar(year, month) {
        // Clear previous calendar
        calendarContainer.innerHTML = '';
        
        // Set month title
        monthTitle.innerHTML = `${getMonthName(month)} ` +
                              `<span id="year-left" class="year-arrow"><</span>` +
                              `<span id="displayed-year">${year}</span>` +
                              `<span id="year-right" class="year-arrow">></span> Schedule`;
        
        // Create calendar grid
        const grid = document.createElement('div');
        grid.className = 'calendar-grid';
        
        // Add day headers
        ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].forEach(day => {
            const dayEl = document.createElement('div');
            dayEl.className = 'day-header';
            dayEl.textContent = day;
            grid.appendChild(dayEl);
        });

        // Calculate calendar dates
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Add empty cells for days before the 1st
        for (let i = 0; i < firstDay; i++) {
            grid.appendChild(createDayElement('', true));
        }

        // Add days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isPast = date < today;
            const isToday = date.getTime() === today.getTime();
            console.log(`Date: ${date}, IsPast: ${isPast}, IsToday: ${isToday}`); // Debug log
            grid.appendChild(createDayElement(day, false, isPast, isToday));
        }

        // Add empty cells to complete the grid
        const totalCells = firstDay + daysInMonth;
        const remainingCells = (7 - (totalCells % 7)) % 7;
        for (let i = 0; i < remainingCells; i++) {
            grid.appendChild(createDayElement('', true));
        }

        calendarContainer.appendChild(grid);

        // Re-attach year button listeners
        setupYearButtons();
    }

    // Create a single day element
    function createDayElement(day, isOtherMonth, isPast = false, isToday = false) {
        const dayEl = document.createElement('div');
        dayEl.className = 'day';
        dayEl.textContent = day;

        if (isOtherMonth) {
            dayEl.classList.add('other-month');
        } else {
            if (isPast) {
                dayEl.classList.add('past-date');
            }
            if (isToday) {
                dayEl.classList.add('today');
            }
            
            // Only add click handler if it's a current/future date
            if (!isPast) {
                dayEl.addEventListener('click', function() {
                    const formattedDate = formatDate(currentYear, currentMonth, day);
                    window.location.href = `/airlyft/aircraftsnpassenger/aircraft-selection.php?date=${formattedDate}`;
                });
            }
        }

        return dayEl;
    }

    // Helper functions
    function getMonthName(monthIndex) {
        const months = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthIndex];
    }

    function formatDate(year, month, day) {
        return `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    }

    function setupMonthButtons() {
        document.querySelectorAll('.nav-button').forEach(button => {
            button.addEventListener('click', function() {
                currentYear = parseInt(this.dataset.year);
                currentMonth = parseInt(this.dataset.monthIndex);
                renderCalendar(currentYear, currentMonth);
                highlightCurrentMonthButton();
            });
        });
    }

    function setupYearButtons() {
        const yearLeftBtn = document.getElementById('year-left');
        const yearRightBtn = document.getElementById('year-right');
        if (yearLeftBtn) {
            yearLeftBtn.addEventListener('click', () => {
                currentYear--;
                updateMonthButtons(currentYear);
                renderCalendar(currentYear, currentMonth);
                highlightCurrentMonthButton();
            });
        }
        if (yearRightBtn) {
            yearRightBtn.addEventListener('click', () => {
                currentYear++;
                updateMonthButtons(currentYear);
                renderCalendar(currentYear, currentMonth);
                highlightCurrentMonthButton();
            });
        }
    }

    function updateMonthButtons(year) {
        const monthNav = document.querySelector('.month-navigation');
        monthNav.innerHTML = '';
        const months = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
        months.forEach((month, index) => {
            const isCurrent = index === currentMonth && year === today.getFullYear();
            const button = document.createElement('button');
            button.className = isCurrent ? 'nav-button current-month' : 'nav-button';
            button.dataset.year = year;
            button.dataset.monthIndex = index;
            button.textContent = month;
            monthNav.appendChild(button);
        });
        setupMonthButtons(); // Re-attach event listeners
    }

    function highlightCurrentMonthButton() {
        document.querySelectorAll('.nav-button').forEach(button => {
            const isCurrent = parseInt(button.dataset.year) === currentYear && 
                             parseInt(button.dataset.monthIndex) === currentMonth;
            button.classList.toggle('current-month', isCurrent);
        });
    }

    // Start the calendar
    init();
});