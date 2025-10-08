// Common login check script for all pages
// This script checks if user is logged in and updates the navigation accordingly

document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
});

function checkLoginStatus() {
    fetch("afterLogin.php", {method: "post"})
    .then(response => response.text())
    .then(data => {
        // Only proceed if we got a valid username (not empty)
        if(data && data.trim() !== '') {
            updateNavigationForLoggedInUser(data);
        }
        // If no valid cookie, navigation stays in default logged-out state
    })
    .catch(error => {
        console.error('Error checking login status:', error);
    });
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
        
        <!-- History icon with dropdown: visible only when logged in -->
        <div class="history dropdown" style="display: block;">
            <i class="fas fa-clock"></i>
            <ul class="dropdown-menu">
                <li><a href="review(n buy).html #order_main">Past orders</a></li>
                <li><a href="myreservation.html">My reservations</a></li>
            </ul>
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
            
            // Reset navigation to logged-out state
            const rightNav = document.querySelector('.right');
            if (rightNav) {
                rightNav.innerHTML = `
                    <!--Search icon-->
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    
                    <div class="login" id="lr">
                        <button><a href="login.html#Login">Login</a></button>&nbsp;&nbsp;
                        <button><a href="login.html#Register">Register</a></button>
                    </div>
                    
                    <div class="cart" id="logOUT">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                `;
            }
            
            // Force a page reload to ensure clean state
            setTimeout(() => {
                window.location.reload();
            }, 100);
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
    });
}
