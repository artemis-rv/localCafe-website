// Login protection script for pages that require authentication
// This script checks if user is logged in and redirects to login page if not

document.addEventListener('DOMContentLoaded', function() {
    checkLoginRequired();
});

function checkLoginRequired() {
    fetch("afterLogin.php", {method: "post"})
    .then(response => response.text())
    .then(data => {
        // If no valid username (empty or null), redirect to login
        if(!data || data.trim() === '') {
            // Show a message before redirecting
            showLoginRequiredMessage();
            
            // Redirect to login page after a short delay
            setTimeout(() => {
                window.location.href = "login.html#Login";
            }, 2000);
        } else {
            // User is logged in, proceed with normal login check
            updateNavigationForLoggedInUser(data);
        }
    })
    .catch(error => {
        console.error('Error checking login status:', error);
        // On error, assume not logged in and redirect
        showLoginRequiredMessage();
        setTimeout(() => {
            window.location.href = "login.html#Login";
        }, 2000);
    });
}

function showLoginRequiredMessage() {
    // Create and show a message overlay
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        font-family: Arial, sans-serif;
    `;
    
    const messageBox = document.createElement('div');
    messageBox.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 400px;
        margin: 20px;
    `;
    
    messageBox.innerHTML = `
        <div style="color: #e74c3c; font-size: 48px; margin-bottom: 20px;">
            <i class="fas fa-lock"></i>
        </div>
        <h2 style="color: #2c3e50; margin-bottom: 15px;">Login Required</h2>
        <p style="color: #7f8c8d; margin-bottom: 20px;">
            You need to be logged in to access this page.
        </p>
        <p style="color: #95a5a6; font-size: 14px;">
            Redirecting to login page...
        </p>
        <div style="margin-top: 20px;">
            <div style="width: 100%; height: 4px; background: #ecf0f1; border-radius: 2px; overflow: hidden;">
                <div style="width: 100%; height: 100%; background: #3498db; animation: loading 2s linear;"></div>
            </div>
        </div>
        <style>
            @keyframes loading {
                from { transform: translateX(-100%); }
                to { transform: translateX(0%); }
            }
        </style>
    `;
    
    overlay.appendChild(messageBox);
    document.body.appendChild(overlay);
}

function updateNavigationForLoggedInUser(username) {
    // Find the right navigation element
    const rightNav = document.querySelector('.right');
    if (!rightNav) return;

    // Update the navigation to show logged-in state
    rightNav.innerHTML = `
        <!--Search icon-->
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search...">
        </div>
        
        <div class="login" id="lr">
            <p>Welcome, ${username}!</p>
        </div>
        
        <div class="cart" id="logOUT">
            <a href="#" id="logout"><i class="fa fa-sign-out"></i></a>
        </div>
    `;

    // Adjust positioning
    rightNav.style.position = 'relative';
    rightNav.style.top = '-0.8em';

    // Add logout event listener
    document.getElementById("logout").addEventListener("click", function(e) {
        e.preventDefault();
        logout();
    });
}

function logout() {
    fetch("logout.php", { method: "post" })
    .then(response => response.text())
    .then(data => {
        if (data.trim() == "logout") {
            // Clear any client-side cookies as well
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
            
            // Redirect to home page after logout
            window.location.href = "index(24dce).html";
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
    });
}
