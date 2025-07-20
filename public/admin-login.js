
// Admin credentials (in a real application, this would be handled on the server)
const ADMIN_CREDENTIALS = {
    username: 'admin',
    password: 'admin123'
};

// Check if already logged in
if (localStorage.getItem('adminLoggedIn') === 'true') {
    window.location.href = 'admin-dashboard.html';
}

document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    
    // Validate credentials
    if (username === ADMIN_CREDENTIALS.username && password === ADMIN_CREDENTIALS.password) {
        // Set login status
        localStorage.setItem('adminLoggedIn', 'true');
        localStorage.setItem('loginTime', new Date().toISOString());
        
        // Hide error message
        errorMessage.style.display = 'none';
        
        // Redirect to admin dashboard
        window.location.href = 'admin-dashboard.html';
    } else {
        // Show error message
        errorMessage.style.display = 'block';
        
        // Clear password field
        document.getElementById('password').value = '';
    }
});

// Auto-focus on username field
document.getElementById('username').focus();