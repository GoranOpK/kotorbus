// --- GLOBALNO DEFINISANE FUNKCIJE (mora biti van DOMContentLoaded) ---
function fetchAvailableSlotsForDate(date, callback) {
  fetch('/api/timeslots/available?date=' + encodeURIComponent(date))
    .then(res => res.json())
    .then(slots => {
      callback(slots); 
    });
}

function populateTimeSlotSelect(selectId, times) {
  const select = document.getElementById(selectId);
  select.innerHTML = '<option value="">Select time slot</option>';
  times.forEach(time => {
    const option = document.createElement('option');
    option.value = time;
    option.textContent = time;
    select.appendChild(option);
  });
}

function filterTimeSlots() {
  const arrivalSelect = document.getElementById('arrival-time-slot');
  const departureSelect = document.getElementById('departure-time-slot');
  const allArrivalOptions = Array.from(arrivalSelect.options).map(opt => opt.value).filter(Boolean);
  const allDepartureOptions = Array.from(departureSelect.options).map(opt => opt.value).filter(Boolean);

  const arrivalTime = arrivalSelect.value;
  const departureTime = departureSelect.value;

  if (arrivalTime) {
    const prevDeparture = departureSelect.value;
    departureSelect.innerHTML = '<option value="">Select time slot</option>';
    allDepartureOptions.forEach(time => {
      if (time > arrivalTime) {
        const option = document.createElement('option');
        option.value = time;
        option.textContent = time;
        departureSelect.appendChild(option);
      }
    });

    if (prevDeparture && prevDeparture > arrivalTime) {
      departureSelect.value = prevDeparture;
    } else {
      departureSelect.value = '';
    }
  }

  if (departureTime) {
    const prevArrival = arrivalSelect.value;
    arrivalSelect.innerHTML = '<option value="">Select time slot</option>';
    allArrivalOptions.forEach(time => {
      if (time < departureTime) {
        const option = document.createElement('option');
        option.value = time;
        option.textContent = time;
        arrivalSelect.appendChild(option);
      }
    });

    if (prevArrival && prevArrival < departureTime) {
      arrivalSelect.value = prevArrival;
    } else {
      arrivalSelect.value = '';
    }
  }

  checkFreeParking();
}

async function reserveSlot() {
  const reservationDate = document.getElementById('reservation_date').value;
  const arrivalTimeStr = document.getElementById('arrival-time-slot').value;
  const departureTimeStr = document.getElementById('departure-time-slot').value;
  const company = document.getElementById('company_name').value.trim();
  const country = document.getElementById('country-input').value.trim();
  const registration = document.getElementById('registration-input').value.trim();
  const email = document.getElementById('email').value.trim();
  const vehicleTypeSelect = document.getElementById('vehicle_type_id');
  const vehicleTypeId = vehicleTypeSelect.value;
  const selectedOption = vehicleTypeSelect.selectedOptions[0];
  const vehiclePrice = selectedOption ? selectedOption.getAttribute('data-price') : null;

  // Fetch slots
  const slotsResponse = await fetch('/api/timeslots');
  const slotsData = await slotsResponse.json();
  const slots = slotsData.data || slotsData;
  const arrivalSlot = slots.find(slot => slot.time_slot.startsWith(arrivalTimeStr));
  const departureSlot = slots.find(slot => slot.time_slot.startsWith(departureTimeStr));

  if (!arrivalSlot || !departureSlot) {
    alert('Could not find the selected time slot!');
    return;
  }

  const data = {
    drop_off_time_slot_id: arrivalSlot.id,
    pick_up_time_slot_id: departureSlot.id,
    reservation_date: reservationDate,
    user_name: company,
    country: country,
    license_plate: registration,
    vehicle_type_id: vehicleTypeId,
    email: email
  };

  try {
    // CSRF zaštita: Prvo povuci CSRF cookie pa onda POST rezervaciju!
    await fetch('https://localhost:8000/sanctum/csrf-cookie', { credentials: 'include' });

    const res = await fetch('https://localhost:8000/api/reservations/reserve', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
      },
      credentials: 'include',
      body: JSON.stringify(data)
    });

    const response = await res.json();

    if (response.success) {
      // Store in localStorage
      localStorage.setItem('reserved_vehicle_type_id', vehicleTypeId);
      localStorage.setItem('reserved_vehicle_type_price', vehiclePrice);
      // (Optional) Store other form data as needed

      alert('Reservation successful!');
      window.location.href = "payment-form.html";
    } else {
      alert('Reservation failed!');
    }
  } catch (err) {
    alert('An error occurred during reservation.');
    console.error(err);
  }
}

function checkFreeParking() {
  const arrivalSelect = document.getElementById('arrival-time-slot');
  const departureSelect = document.getElementById('departure-time-slot');
  const reserveBtn = document.getElementById('reserve-btn');
  const freeMsg = document.getElementById('free-parking-msg');

  // Get all option values except the placeholder
  const arrivalOptions = Array.from(arrivalSelect.options).filter(opt => opt.value);
  const departureOptions = Array.from(departureSelect.options).filter(opt => opt.value);

  // Check if first arrival and last departure are selected
  const isFirstArrival = arrivalSelect.value === arrivalOptions[0]?.value;
  const isLastDeparture = departureSelect.value === departureOptions[departureOptions.length - 1]?.value;

  if (isFirstArrival && isLastDeparture) {
    reserveBtn.disabled = true;
    reserveBtn.style.background = "#ccc";
    freeMsg.style.display = "inline";
  } else {
    reserveBtn.disabled = false;
    reserveBtn.style.background = "";
    freeMsg.style.display = "none";
  }
}

const translations = {
  en: {
    pickDate: "Pick a date",
    arrival: "Arrival time",
    departure: "Departure time",
    company: "Company name",
    country: "Country",
    registration: "Registration plates",
    email: "Email",
    vehicleCategory: "Select vehicle category",
    agree: "I agree to the",
    terms: "terms and conditions",
    mustAgree: "You must agree to the terms to reserve a slot.",
    reserve: "Reserve",
    termsTitle: "Terms and Conditions",
    freeParking: "Parking is free for this time segment!"
  },
  mne: {
    pickDate: "Izaberite datum",
    arrival: "Vrijeme dolaska",
    departure: "Vrijeme odlaska",
    company: "Naziv kompanije",
    country: "Država",
    registration: "Registracione tablice",
    email: "Email",
    vehicleCategory: "Izaberite kategoriju vozila",
    agree: "Slažem se sa",
    terms: "uslovima korišćenja",
    mustAgree: "Morate prihvatiti uslove da biste rezervisali termin.",
    reserve: "Rezerviši",
    termsTitle: "Uslovi korišćenja",
    freeParking: "Parking je besplatan za ovaj vremenski segment!"
  }
};

function setLanguage(lang) {
  const ids = [
    ['pick-date-label', 'pickDate'],
    ['arrival-label', 'arrival'],
    ['departure-label', 'departure'],
    ['company_name', 'company', 'placeholder'],
    ['country-input', 'country', 'placeholder'],
    ['registration-input', 'registration', 'placeholder'],
    ['email', 'email', 'placeholder'],
    ['vehicle-category-option', 'vehicleCategory'],
    ['agree-text', 'agree'],
    ['show-terms', 'terms'],
    ['agreement-error', 'mustAgree'],
    ['reserve-btn', 'reserve'],
    ['terms-title', 'termsTitle']
  ];

  ids.forEach(([id, key, attr]) => {
    const el = document.getElementById(id);
    if (el) {
      if (attr === 'placeholder') {
        el.placeholder = translations[lang][key];
      } else {
        el.textContent = translations[lang][key];
      }
    }
  });

  const termsText = {
    en: `
      <p><strong>By using this service, you agree to abide by all rules and regulations set forth by Kotorbus.</strong></p>
      <ul>
        <li>These terms establish the ordering process, payment, and download of the products offered on the kotorbus.me website. The kotorbus.me website is available for private use without any fees and according to the following terms and conditions.</li>
        <li>The Vendor is the Municipality of Kotor and the Buyer is the visitor of this website who completes an electronic request, sends it to the Vendor and conducts a payment using a credit or debit card. The Product is one of the items on offer on the kotorbus.me website – a fee for stopping and parking in a special traffic regulation zone based on the prices established by provisions of the Assembly of the Municipality of Kotor (dependent on bus capacity).</li>
        <li>The Buyer orders the product or products by filling an electronic form. Any person who orders at least one product, enters the required information, and sends their order is considered to be a buyer.</li>
        <li>All the prices are final, shown in EUR. The Vendor, the Municipality of Kotor, as a local authority, is not a taxpayer within the VAT system; therefore the prices on the website do not include VAT.</li>
        <li>To process the services which the Buyer ordered through the website, there are no additional fees incurred on the Buyer.</li>
        <li>The goods and/or services are ordered online. The goods are considered to be ordered when the Buyer selects and confirms a payment method and when the credit or debit card authorization process is successfully terminated. Once the ordering process is completed, the Buyer gets an invoice which serves both as a confirmation of your order/proof of payment and a voucher for the service.</li>
        <li><strong>Payment:</strong> The products and services are paid online by using one of the following debit or credit cards: MasterCard®, Maestro® or Visa.</li>
        <li><strong>General conditions:</strong> Depending on the amount paid, the service is available for the vehicle of selected category, on the date and during the time indicated when making the purchase. The Voucher cannot be used outside the selected period. Once used, the Voucher can no longer be used. The Buyer is responsible for the use of the Voucher. The Municipality of Kotor bears no responsibility for the unauthorized use of the Voucher.</li>
        <li>The Municipality of Kotor reserves the right to change these terms and conditions. Any changes will be applied to the use of the kotorbus.me website. The buyer bears the responsibility for the accuracy and completeness of data during the buying process.</li>
        <li>The services provided by the Municipality of Kotor on the kotorbus.me website do not include the costs incurred by using computer equipment and internet service providers' services to access our website. The Municipality of Kotor is not responsible for any costs, including, but not limited to, telephone bills, Internet traffic bills or any other kind of costs that may be incurred.</li>
        <li>The Buyer does not have the right to a refund.</li>
        <li>The Municipality of Kotor cannot guarantee that the service will be free of errors. If an error occurs, kindly report it to: bus@kotor.me and we shall remove the error as soon as we possibly can.</li>
      </ul>
    `,
    mne: `
      <p><strong>Korišćenjem ove usluge, slažete se da poštujete sva pravila i propise koje je postavio Kotorbus.</strong></p>
      <ul>
        <li>Ovi uslovi definišu proces naručivanja, plaćanja i preuzimanja proizvoda ponuđenih na sajtu kotorbus.me. Sajt kotorbus.me je dostupan za privatnu upotrebu bez naknade i u skladu sa sljedećim uslovima korišćenja.</li>
        <li>Prodavac je Opština Kotor, a Kupac je posjetilac ovog sajta koji popuni elektronski zahtjev, pošalje ga Prodavcu i izvrši plaćanje putem kreditne ili debitne kartice. Proizvod je jedna od stavki u ponudi na sajtu kotorbus.me – naknada za zaustavljanje i parkiranje u zoni posebnog režima saobraćaja prema cijenama utvrđenim odlukom Skupštine Opštine Kotor (u zavisnosti od kapaciteta autobusa).</li>
        <li>Kupac naručuje proizvod ili proizvode popunjavanjem elektronskog formulara. Svako ko naruči makar jedan proizvod, unese potrebne podatke i pošalje narudžbu smatra se kupcem.</li>
        <li>Sve cijene su konačne, iskazane u EUR. Prodavac, Opština Kotor, kao lokalna samouprava, nije obveznik PDV-a; stoga cijene na sajtu ne sadrže PDV.</li>
        <li>Za obradu usluga koje je Kupac naručio putem sajta, Kupcu se ne naplaćuju dodatne takse.</li>
        <li>Roba i/ili usluge se naručuju online. Roba se smatra naručenom kada Kupac izabere i potvrdi način plaćanja i kada se proces autorizacije kreditne ili debitne kartice uspješno završi. Po završetku procesa naručivanja, Kupac dobija fakturu koja služi kao potvrda narudžbe/dokaz o plaćanju i vaučer za uslugu.</li>
        <li><strong>Plaćanje:</strong> Proizvodi i usluge se plaćaju online korišćenjem jedne od sljedećih debitnih ili kreditnih kartica: MasterCard®, Maestro® ili Visa.</li>
        <li><strong>Opšti uslovi:</strong> U zavisnosti od iznosa plaćanja, usluga je dostupna za vozilo izabrane kategorije, na datum i u vremenskom periodu navedenom prilikom kupovine. Vaučer se ne može koristiti van izabranog perioda. Nakon korišćenja, vaučer više nije važeći. Kupac je odgovoran za korišćenje vaučera. Opština Kotor ne snosi odgovornost za neovlašćeno korišćenje vaučera.</li>
        <li>Opština Kotor zadržava pravo izmjene ovih uslova korišćenja. Sve promjene će se primjenjivati na korišćenje sajta kotorbus.me. Kupac snosi odgovornost za tačnost i potpunost podataka tokom procesa kupovine.</li>
        <li>Usluge koje pruža Opština Kotor putem sajta kotorbus.me ne uključuju troškove nastale korišćenjem računarske opreme i usluga internet provajdera za pristup našem sajtu. Opština Kotor nije odgovorna za bilo kakve troškove, uključujući, ali ne ograničavajući se na telefonske račune, račune za internet saobraćaj ili bilo koje druge troškove koji mogu nastati.</li>
        <li>Kupac nema pravo na povraćaj novca.</li>
        <li>Opština Kotor ne može garantovati da će usluga biti bez grešaka. Ukoliko dođe do greške, molimo vas da je prijavite na: bus@kotor.me i uklonićemo je u najkraćem mogućem roku.</li>
      </ul>
    `
  };
  const termsModalDiv = document.getElementById('terms-content');
  if (termsModalDiv) termsModalDiv.innerHTML = termsText[lang];

  const freeMsg = document.getElementById('free-parking-msg');
  if (freeMsg) {
    freeMsg.textContent = translations[lang].freeParking;
  }
}

// --- DOMContentLoaded EVENT HANDLER ---
document.addEventListener('DOMContentLoaded', function () {
  setLanguage('en'); // or 'mne' if you want Montenegrin by default

  // Calculate today's date string first
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  const todayStr = `${yyyy}-${mm}-${dd}`;

  // Set min date for the date input to today
  const reservationDateInput = document.getElementById('reservation_date');
  if (reservationDateInput) {
    reservationDateInput.min = todayStr;
    reservationDateInput.value = todayStr;
    reservationDateInput.dispatchEvent(new Event('change'));
  }

  // Initialize FullCalendar with validRange using todayStr
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: '',
      center: 'title',
      right: 'prev,next'
    },
    validRange: {
      start: todayStr // Only allow selecting from today onwards
    },
    dateClick: function(info) {
      calendar.select(info.date); // <-- This will highlight the clicked date
      reservationDateInput.value = info.dateStr;
      reservationDateInput.dispatchEvent(new Event('change'));
      document.getElementById('slot-section').style.display = 'block';
    }
  });
  calendar.render();

  // Fetch vehicle categories from API and populate select
  fetch('/api/vehicle-types')
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('vehicle_type_id');
      select.innerHTML = '<option value="">Select vehicle category</option>';
      data.forEach(type => {
        const option = document.createElement('option');
        option.value = type.id;
        option.textContent = type.description_vehicle || type.name || type.category || type.title || `Type ${type.id}`;
        option.setAttribute('data-price', type.price);
        select.appendChild(option);
      });
    });

  // Slušaj promene na dropdown-ovima za filtraciju vremena
  document.getElementById('arrival-time-slot').addEventListener('change', filterTimeSlots);
  document.getElementById('departure-time-slot').addEventListener('change', filterTimeSlots);
  // Slušaj promene na dropdown-ovima za filtraciju vremena
  document.getElementById('arrival-time-slot').addEventListener('change', filterTimeSlots);
  document.getElementById('departure-time-slot').addEventListener('change', filterTimeSlots);

  // Kada se promeni datum, ponovo povuci dostupne slotove
  document.getElementById('reservation_date').addEventListener('change', function () {
    const date = this.value;
    fetchAvailableSlotsForDate(date, function(availableSlots) {
      populateTimeSlotSelect('arrival-time-slot', availableSlots.map(s => s.time_slot));
      populateTimeSlotSelect('departure-time-slot', availableSlots.map(s => s.time_slot));
      // Re-attach listeners after repopulating
      document.getElementById('arrival-time-slot').addEventListener('change', filterTimeSlots);
      document.getElementById('departure-time-slot').addEventListener('change', filterTimeSlots);
      // After repopulating time slots in reservation_date change handler
      checkFreeParking();
    });
  });

  document.getElementById('show-terms').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('terms-modal').style.display = 'block';
  });
  document.getElementById('close-terms').addEventListener('click', function() {
    document.getElementById('terms-modal').style.display = 'none';
  });

  document.getElementById('lang-en').addEventListener('click', function() {
    setLanguage('en');
  });
  document.getElementById('lang-cg').addEventListener('click', function() {
    setLanguage('mne');
  });

  document.getElementById('reserve-btn').addEventListener('click', reserveSlot);
});