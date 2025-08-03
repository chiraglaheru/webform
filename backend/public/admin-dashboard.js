const token = localStorage.getItem('accessToken');
const user = JSON.parse(localStorage.getItem('user') || 'null');

// Redirect if no token or user
if (!token || !user) {
    window.location.href = 'admin-login.html';
}

// Logout function
function autoLogout() {
    localStorage.clear();
    window.location.href = 'admin-login.html';
}

// Manual logout
function logout() {
    if (confirm("Are you sure you want to logout?")) {
        autoLogout();
    }
}
document.getElementById("logoutButton").addEventListener("click", logout);

// Setup token expiry auto logout
function setupTokenExpiryLogout(token) {
    try {
        const decoded = jwt_decode(token);
        const expiryTime = decoded.exp * 1000;
        const timeLeft = expiryTime - Date.now();

        if (timeLeft <= 0) {
            alert("Session expired, please login again.");
            autoLogout();
        } else {
            setTimeout(() => {
                alert("Session expired, please login again.");
                autoLogout();
            }, timeLeft);
        }
    } catch (e) {
        console.error("Invalid token:", e);
        autoLogout();
    }
}
setupTokenExpiryLogout(token);

// Setup inactivity-based auto logout
let lastActivity = Date.now();
const INACTIVITY_TIMEOUT = 10 * 60 * 1000; // 10 minutes

function checkInactivity() {
    if (Date.now() - lastActivity > INACTIVITY_TIMEOUT) {
        alert("Logged out due to inactivity.");
        autoLogout();
    }
}
setInterval(checkInactivity, 60 * 1000); // check every minute

// Reset activity on any interaction
['mousemove', 'keypress', 'click', 'scroll'].forEach(evt =>
    document.addEventListener(evt, () => lastActivity = Date.now())
);

// nonteach download
// Separate handler for Excel download
async function handleExcelDownload() {
    try {
        const res = await fetch('http://localhost:3001/export');
        if (!res.ok) throw new Error('Failed to fetch Excel');

        const blob = await res.blob();
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'applicants.xlsx';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } catch (err) {
        console.error(err);
        alert("Error downloading Excel: " + err.message);
    }
}

// Attach the event listener
// document.getElementById('download-excel').addEventListener('click', handleExcelDownload);


// nonteach PDF download
document.getElementById('download-pdf').addEventListener('click', async () => {
    try {
        const res = await fetch('http://localhost:3001/export/pdf', {
            headers: { 'Content-Type': 'application/pdf' }
        });
        if (!res.ok) throw new Error('Failed to fetch PDF');

        const blob = await res.blob();
        const url = URL.createObjectURL(blob);

        const a = document.createElement('a');
        a.href = url;
        a.download = 'applicants.pdf';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    } catch (err) {
        console.error(err);
        alert("Error downloading PDF: " + err.message);
    }
});

