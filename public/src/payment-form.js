const CSRF_TOKEN = 'QvfNezVoOGu65W0CmVX3sM2VdQ7bsJL7Fgm65OGj';

// Postavi vehicle_type_id iz localStorage na učitavanje stranice
document.addEventListener('DOMContentLoaded', function() {
    const vehicleTypeId = localStorage.getItem('reserved_vehicle_type_id');
    if (vehicleTypeId) {
        document.getElementById('vehicle_type_id_hidden').value = vehicleTypeId;
    } else {
        alert("Nije odabran tip vozila. Vratite se na rezervaciju.");
        window.location.href = "index.html";
    }
});

// Funkcija za enkodiranje forme
function encodeFormData(data) {
    return Array.from(data.entries()).map(
        ([key, value]) => encodeURIComponent(key) + '=' + encodeURIComponent(value)
    ).join('&');
}

// Submit handler za formu
document.getElementById('payment-form').onsubmit = async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    const res = await fetch('/procesiraj-placanje', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: encodeFormData(formData)
    });

    let json;
    try {
        json = await res.json();
    } catch (err) {
        document.getElementById('payment-result').innerText = "Greška u komunikaciji sa serverom.";
        return;
    }
    document.getElementById('payment-result').innerText = json.message || JSON.stringify(json);
};