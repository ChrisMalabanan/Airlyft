document.addEventListener('DOMContentLoaded', () => {
    const elements = {
        aircraftSelect: document.getElementById('aircraft-select'),
        displaySelectedDate: document.getElementById('display-selected-date'),
        hiddenSelectedDate: document.getElementById('hidden-selected-date'),
        hiddenAircraftRate: document.getElementById('hidden-aircraft-rate'),
        aircraftNameDisplay: document.getElementById('aircraft-name'),
        aircraftCapacity: document.getElementById('aircraft-capacity'),
        aircraftDescription: document.getElementById('aircraft-description'),
        aircraftRateDisplay: document.getElementById('aircraft-rate-display'),
        aircraftImage: document.getElementById('aircraft-image'),
        aircraftImageContainer: document.querySelector('.aircraft-image-container'),
        passengerCountInput: document.getElementById('passenger-count'),
        passengerFieldsContainer: document.getElementById('passenger-fields-container'),
        bookingForm: document.getElementById('bookingForm')
    };

    const aircraftData = {
        "Cessna Turbo Stationair HD (T206H)": { capacity: "Up to 5 passengers", maxPassengers: 5, description: "Versatile aircraft for short to medium-range flights.", rate: "50000", image: "cess206.png", id: 1 },
        "Cessna Grand Caravan EX (Deluxe Config)": { capacity: "Up to 12 passengers", maxPassengers: 12, description: "Reliable turboprop for larger groups.", rate: "120000", image: "cessnagc.png", id: 2 },
        "Airbus H160": { capacity: "Up to 10 passengers", maxPassengers: 10, description: "Medium twin-engine helicopter for executive transport.", rate: "300000", image: "airbus.png", id: 3 },
        "Sikorsky S-76D": { capacity: "Up to 12 passengers", maxPassengers: 12, description: "Sophisticated helicopter for long-range flights.", rate: "450000", image: "sikorsky.png", id: 4 }
    };

    // Initialize urlParams at the top level
    const urlParams = new URLSearchParams(window.location.search);

    // Normalize date from URL
    let selectedDate = urlParams.get('date') || new Date().toISOString().slice(0, 10);
    try {
        selectedDate = new Date(selectedDate).toISOString().slice(0, 10);
        console.log('Normalized selectedDate:', selectedDate);
    } catch (e) {
        console.error('Error parsing date:', e);
        selectedDate = new Date().toISOString().slice(0, 10);
    }
    elements.displaySelectedDate.textContent = selectedDate;
    elements.hiddenSelectedDate.value = selectedDate;

    // Function to check availability for all aircraft
    async function checkAllAircraftAvailability(date) {
        const availabilityResults = {};
        
        for (const [aircraftName, details] of Object.entries(aircraftData)) {
            try {
                const response = await fetch('../aircraftsnpassenger/check_booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ date, aircraft: details.id })
                });
                
                const result = await response.json();
                availabilityResults[aircraftName] = result.available;
            } catch (error) {
                console.error(`Error checking availability for ${aircraftName}:`, error);
                availabilityResults[aircraftName] = true; // Assume available if error
            }
        }
        
        return availabilityResults;
    }

    // Function to update dropdown options with availability status
    function updateAircraftDropdownOptions(availability) {
        const select = elements.aircraftSelect;
        const currentValue = select.value;
        
        // Clear existing options (keeping the first default option)
        while (select.options.length > 1) {
            select.remove(1);
        }
        
        // Add options with availability styling
        for (const [aircraftName, details] of Object.entries(aircraftData)) {
            const option = document.createElement('option');
            option.value = aircraftName;
            option.textContent = aircraftName;
            
            if (!availability[aircraftName]) {
                option.textContent += ' (Booked)';
                option.style.color = 'red';
                option.disabled = true;
            }
            
            select.appendChild(option);
        }
        
        // Restore previously selected value if it's still available
        if (currentValue && availability[currentValue]) {
            select.value = currentValue;
        }
        
        updateAircraftDetails();
    }

    // Initialize availability check on page load
    checkAllAircraftAvailability(selectedDate).then(updateAircraftDropdownOptions);

    // Update availability when date changes
    elements.hiddenSelectedDate.addEventListener('change', async (e) => {
        const newDate = e.target.value;
        elements.displaySelectedDate.textContent = newDate;
        const availability = await checkAllAircraftAvailability(newDate);
        updateAircraftDropdownOptions(availability);
    });

    function updateAircraftDetails() {
        const selectedAircraft = elements.aircraftSelect.value;
        const details = aircraftData[selectedAircraft] || {};
        const defaults = { capacity: '', description: '', rate: '', image: '', id: null };

        elements.aircraftNameDisplay.textContent = selectedAircraft || '';
        elements.aircraftCapacity.textContent = details.capacity || defaults.capacity;
        elements.aircraftDescription.textContent = details.description || defaults.description;
        elements.aircraftRateDisplay.textContent = details.rate ? `P${details.rate}/hour` : defaults.rate;
        elements.hiddenAircraftRate.value = details.rate || defaults.rate;
        elements.aircraftImage.src = details.image ? `aircraft/${details.image}` : defaults.image;
        elements.aircraftImageContainer.style.display = details.image ? 'block' : 'none';

        const maxPassengers = details.maxPassengers || 1;
        elements.passengerCountInput.setAttribute('max', maxPassengers);
        elements.passengerCountInput.disabled = !selectedAircraft;
        elements.passengerCountInput.value = Math.min(Math.max(parseInt(elements.passengerCountInput.value) || 1, 1), maxPassengers);
        generatePassengerFields(parseInt(elements.passengerCountInput.value));
    }

    function generatePassengerFields(count) {
        elements.passengerFieldsContainer.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const div = document.createElement('div');
            div.classList.add('passenger-entry');
            div.innerHTML = `
                <h3>Passenger ${i + 1}</h3>
                <div class="form-group">
                    <label for="fname-${i}">First Name:</label>
                    <input type="text" id="fname-${i}" name="passengers[${i}][fname]" required>
                </div>
                <div class="form-group">
                    <label for="lname-${i}">Last Name:</label>
                    <input type="text" id="lname-${i}" name="passengers[${i}][lname]" required>
                </div>
                <div class="form-group">
                    <label for="age-${i}">Age:</label>
                    <input type="number" id="age-${i}" name="passengers[${i}][age]" min="1" required>
                </div>
                <div class="form-group">
                    <label for="street-${i}">Street:</label>
                    <input type="text" id="street-${i}" name="passengers[${i}][street]" required>
                </div>
                <div class="form-group">
                    <label for="barangay-${i}">Barangay:</label>
                    <input type="text" id="barangay-${i}" name="passengers[${i}][barangay]" required>
                </div>
                <div class="form-group">
                    <label for="municipality-${i}">Municipality/City:</label>
                    <input type="text" id="municipality-${i}" name="passengers[${i}][municipality]" required>
                </div>
                <div class="form-group">
                    <label for="province-${i}">Province:</label>
                    <input type="text" id="province-${i}" name="passengers[${i}][province]" required>
                </div>
                <div class="form-group">
                    <label>Has Insurance:</label>
                    <div class="insurance-options">
                        <input type="radio" id="insurance-yes-${i}" name="passengers[${i}][hasInsurance]" value="yes">
                        <label for="insurance-yes-${i}">Yes</label>
                        <input type="radio" id="insurance-no-${i}" name="passengers[${i}][hasInsurance]" value="no" checked>
                        <label for="insurance-no-${i}">No</label>
                    </div>
                </div>
                <div class="form-group insurance-details-group" id="insurance-details-${i}" style="display:none;">
                    <label for="insurance-info-${i}">Insurance Details:</label>
                    <textarea id="insurance-info-${i}" name="passengers[${i}][insuranceDetails]" rows="3" placeholder="Enter insurance provider and policy number"></textarea>
                </div>
            `;
            elements.passengerFieldsContainer.appendChild(div);

            div.querySelector(`#insurance-yes-${i}`).addEventListener('change', () => {
                const textarea = div.querySelector(`#insurance-info-${i}`);
                div.querySelector(`#insurance-details-${i}`).style.display = 'block';
                textarea.setAttribute('required', 'required');
            });
            div.querySelector(`#insurance-no-${i}`).addEventListener('change', () => {
                const textarea = div.querySelector(`#insurance-info-${i}`);
                div.querySelector(`#insurance-details-${i}`).style.display = 'none';
                textarea.removeAttribute('required');
                textarea.value = '';
            });
        }
    }

    elements.passengerCountInput.addEventListener('change', (e) => {
        const count = parseInt(e.target.value);
        const max = parseInt(e.target.getAttribute('max')) || 1;
        e.target.value = Math.min(Math.max(count, 1), max);
        generatePassengerFields(parseInt(e.target.value));
    });

    elements.aircraftSelect.addEventListener('change', updateAircraftDetails);

    elements.bookingForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const aircraftName = elements.aircraftSelect.value;
        const selectedDate = elements.hiddenSelectedDate.value;
        const aircraftRate = parseFloat(elements.hiddenAircraftRate.value);
        const passengerCount = parseInt(elements.passengerCountInput.value);

        if (!aircraftName || !selectedDate || isNaN(aircraftRate) || passengerCount < 1 || !aircraftData[aircraftName]) {
            alert('Please ensure all required fields are filled and a valid aircraft is selected.');
            return;
        }

        const passengerDivs = elements.passengerFieldsContainer.querySelectorAll('.passenger-entry');
        for (let i = 0; i < passengerDivs.length; i++) {
            const hasInsurance = passengerDivs[i].querySelector(`input[name="passengers[${i}][hasInsurance]"]:checked`).value;
            if (hasInsurance === 'yes' && !passengerDivs[i].querySelector(`#insurance-info-${i}`).value.trim()) {
                alert(`Please provide insurance details for Passenger ${i + 1}.`);
                passengerDivs[i].querySelector(`#insurance-info-${i}`).focus();
                return;
            }
        }

        try {
            console.log('Sending to check_booking.php:', { date: selectedDate, aircraft: aircraftData[aircraftName].id });
            const response = await fetch('../aircraftsnpassenger/check_booking.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date: selectedDate, aircraft: aircraftData[aircraftName].id }),
                signal: AbortSignal.timeout(5000)
            });

            const responseClone = response.clone();
            let result;
            try {
                result = await response.json();
            } catch (e) {
                const errorText = await responseClone.text();
                console.error('Invalid JSON response:', response.status, errorText.slice(0, 200));
                throw new Error(`Invalid JSON response: ${errorText.slice(0, 200)}`);
            }

            console.log('Availability check response:', result);

            if (!response.ok) {
                throw new Error(`Server error: ${result.error || 'Unknown error'} (Status: ${response.status})`);
            }

            if (result.available) {
                const bookingDetails = {
                    bookingId: 'TEMP-' + Date.now(),
                    bookingDate: new Date().toISOString().slice(0, 10),
                    userName: 'Guest User',
                    placeName: urlParams.get('placeName') || 'Batangas',
                    departureCoords: urlParams.get('departureCoords') || 'MNL',
                    arrivalCoords: urlParams.get('placeName') || 'CEB',
                    depDateTime: selectedDate + ' 08:00:00',
                    arrDateTime: selectedDate + ' 16:00:00',
                    scheduleStatus: 'Pending Payment',
                    liftModel: aircraftName,
                    liftCapacity: aircraftData[aircraftName].capacity,
                    basePrice: (aircraftRate * 1).toFixed(2),
                    totalAmount: (aircraftRate * 1).toFixed(2),
                    aircraftRate: aircraftRate.toFixed(2),
                    passengerCount
                };

                const passengers = Array.from(passengerDivs).map((div, i) => ({
                    fname: div.querySelector(`#fname-${i}`).value,
                    lname: div.querySelector(`#lname-${i}`).value,
                    age: div.querySelector(`#age-${i}`).value,
                    street: div.querySelector(`#street-${i}`).value,
                    barangay: div.querySelector(`#barangay-${i}`).value,
                    municipality: div.querySelector(`#municipality-${i}`).value,
                    province: div.querySelector(`#province-${i}`).value,
                    hasInsurance: div.querySelector(`input[name="passengers[${i}][hasInsurance]"]:checked`).value,
                    insuranceDetails: div.querySelector(`#insurance-info-${i}`).value
                }));

                const queryParams = new URLSearchParams(bookingDetails).toString();
                window.location.href = `../payment/payment2.php?${queryParams}&passengers=${encodeURIComponent(JSON.stringify(passengers))}`;
            } else {
                alert(`This aircraft is already booked for the selected date. ${result.error || 'Please choose another date or aircraft.'}`);
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            alert(`Error checking availability: ${error.message}. Please try again.`);
        }
    });
});