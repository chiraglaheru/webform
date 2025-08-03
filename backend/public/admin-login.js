// admin-login.js

// Get DOM elements
const loginForm = document.getElementById('loginForm');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');
const errorMessage = document.getElementById('errorMessage');

// Check if already logged in
const accessToken = localStorage.getItem('accessToken');
if (accessToken) {
    window.location.href = 'admin-dashboard.html';
}

// Redirect on success
function redirectToDashboard() {
    window.location.href = 'admin-dashboard.html';
}

// Login form handler
loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = usernameInput.value;
    const password = passwordInput.value;

    errorMessage.style.display = 'none';
    errorMessage.textContent = '';

    try {
        const response = await fetch('http://localhost:3000/api/auth/signin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });

        const data = await response.json();

        if (response.ok) {
            localStorage.setItem('accessToken', data.accessToken);
            localStorage.setItem('user', JSON.stringify({ id: data.id, username: data.username }));
            redirectToDashboard();
        } else {
            errorMessage.textContent = data.message || 'Login failed. Please try again.';
            errorMessage.style.display = 'block';
            passwordInput.value = '';
        }
    } catch (error) {
        console.error('Login error:', error);
        errorMessage.textContent = 'Network/server error. Please try again later.';
        errorMessage.style.display = 'block';
        passwordInput.value = '';
    }
});

usernameInput.focus();
