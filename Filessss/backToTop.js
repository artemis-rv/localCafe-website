// Back to Top Button Functionality
// This script creates a smooth scrolling back to top button

document.addEventListener('DOMContentLoaded', function() {
    // First try to enhance existing buttons
    enhanceExistingBackToTopButton();
    
    // If no existing button found, create a new one
    if (!document.querySelector('button a[href="#head"]') && !document.getElementById('backToTopBtn')) {
        createBackToTopButton();
    }
});

function createBackToTopButton() {
    // Create the back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.id = 'backToTopBtn';
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.setAttribute('aria-label', 'Back to top');
    
    // Style the button
    backToTopBtn.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 50px;
        height: 50px;
        background: #4c3228;
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    
    // Add hover effects
    backToTopBtn.addEventListener('mouseenter', function() {
        this.style.background = '#a08679'; 
        this.style.transform = 'scale(1.1)';
    });
    
    backToTopBtn.addEventListener('mouseleave', function() {
        this.style.background = '#4c3228';
        this.style.transform = 'scale(1)';
    });
    
    // Add click event for smooth scrolling
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add the button to the page
    document.body.appendChild(backToTopBtn);
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.opacity = '1';
            backToTopBtn.style.visibility = 'visible';
        } else {
            backToTopBtn.style.opacity = '0';
            backToTopBtn.style.visibility = 'hidden';
        }
    });
    
    // Add keyboard accessibility
    backToTopBtn.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    });
}

// Alternative function for pages that already have a back to top button
function enhanceExistingBackToTopButton() {
    const existingBtn = document.querySelector('button a[href="#head"]');
    if (existingBtn) {
        const button = existingBtn.parentElement;
        
        // Remove the link and add click event
        button.removeChild(existingBtn);
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        
        // Style the button
        button.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: #4c3228;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        // Add hover effects
        button.addEventListener('mouseenter', function() {
            this.style.background = '#2980b9';
            this.style.transform = 'scale(1.1)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.background = '#3498db';
            this.style.transform = 'scale(1)';
        });
        
        // Add click event for smooth scrolling
        button.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                button.style.opacity = '1';
                button.style.visibility = 'visible';
            } else {
                button.style.opacity = '0';
                button.style.visibility = 'hidden';
            }
        });
    }
}
