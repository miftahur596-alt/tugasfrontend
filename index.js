function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const responseMessage = document.getElementById('responseMessage');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                nama: formData.get('nama'),
                email: formData.get('email'),
                telepon: formData.get('telepon'),
                pesan: formData.get('pesan')
            };

            if (!validateForm(data)) {
                showMessage('Mohon isi semua field dengan benar!', 'error');
                return;
            }

            sendFormData(data);
        });
    }
});

function validateForm(data) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex = /^(\+62|0)[0-9]{8,12}$/;

    if (!data.nama || data.nama.trim() === '') {
        return false;
    }

    if (!emailRegex.test(data.email)) {
        return false;
    }

    if (!phoneRegex.test(data.telepon)) {
        return false;
    }

    if (!data.pesan || data.pesan.trim() === '') {
        return false;
    }

    return true;
}

function sendFormData(data) {
    // For now, we'll simulate sending to server
    // In production, this would send to proses.php

    fetch('proses.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showMessage('Pesan berhasil dikirim! Terima kasih telah menghubungi kami.', 'success');
            document.getElementById('contactForm').reset();
        } else {
            showMessage(result.message || 'Gagal mengirim pesan. Silakan coba lagi.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Terjadi kesalahan. Silakan coba lagi.', 'error');
    });
}

function showMessage(message, type) {
    const responseMessage = document.getElementById('responseMessage');
    responseMessage.textContent = message;
    responseMessage.className = 'response-message ' + type;

    setTimeout(() => {
        responseMessage.className = 'response-message';
    }, 5000);
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
