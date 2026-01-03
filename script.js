function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                document.getElementById('navLinks').classList.remove('active');
            }
        });
    });

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const alertBox = document.getElementById('alertBox');
            const formData = {
                nama: document.getElementById('nama').value,
                email: document.getElementById('email').value,
                telepon: document.getElementById('telepon').value,
                minat: document.getElementById('minat').value,
                pesan: document.getElementById('pesan').value
            };

            if (!formData.nama || !formData.email || !formData.telepon || !formData.minat) {
                showAlert('Mohon lengkapi semua field yang wajib diisi!', 'error');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                showAlert('Format email tidak valid!', 'error');
                return;
            }

            const phoneRegex = /^[0-9]{10,13}$/;
            if (!phoneRegex.test(formData.telepon.replace(/[-\s]/g, ''))) {
                showAlert('Nomor telepon harus 10-13 digit!', 'error');
                return;
            }

            fetch('proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if(data.success) {
                    showAlert(data.message, 'success');
                    contactForm.reset();
                } else {
                    showAlert(data.message || 'Terjadi kesalahan. Silakan coba lagi.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan koneksi: ' + error.message, 'error');
            });
        });
    }

    document.querySelectorAll('input, textarea, select').forEach(element => {
        element.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        element.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

function showAlert(message, type) {
    const alertBox = document.getElementById('alertBox');
    alertBox.className = 'alert alert-' + type;
    alertBox.textContent = message;
    alertBox.style.display = 'block';
    
    setTimeout(() => {
        alertBox.style.display = 'none';
    }, 5000);
}

document.addEventListener('click', function(event) {
    const navLinks = document.getElementById('navLinks');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (navLinks && menuToggle) {
        if (!navLinks.contains(event.target) && !menuToggle.contains(event.target)) {
            navLinks.classList.remove('active');
        }
    }
});