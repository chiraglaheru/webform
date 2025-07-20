
// Check if user is logged in
if (localStorage.getItem('adminLoggedIn') !== 'true') {
    window.location.href = 'admin-login.html';
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        localStorage.removeItem('adminLoggedIn');
        localStorage.removeItem('loginTime');
        window.location.href = 'admin-login.html';
    }
}

// Auto-logout after 2 hours of inactivity
let lastActivity = Date.now();
const AUTO_LOGOUT_TIME = 2 * 60 * 60 * 1000; // 2 hours

function checkAutoLogout() {
    if (Date.now() - lastActivity > AUTO_LOGOUT_TIME) {
        alert('Session expired. Please login again.');
        logout();
    }
}

// Track user activity
document.addEventListener('click', () => lastActivity = Date.now());
document.addEventListener('keypress', () => lastActivity = Date.now());

// Check auto-logout every minute
setInterval(checkAutoLogout, 60000);

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', initializeDashboard);