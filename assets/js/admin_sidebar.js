// Initialize WOW.js for animations (if available)
if (typeof WOW !== 'undefined') {
    new WOW().init();
}

// Get DOM elements - check if they exist first
const menuToggle = document.getElementById('menuToggle');
const adminSidebar = document.getElementById('adminSidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const closeSidebar = document.getElementById('closeSidebar');

// Function to open mobile sidebar
function openSidebar() {
    if (adminSidebar && sidebarOverlay) {
        adminSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

// Function to close mobile sidebar
function closeSidebarFunc() {
    if (adminSidebar && sidebarOverlay) {
        adminSidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Event listeners for mobile sidebar toggle (only if elements exist)
if (menuToggle) {
    menuToggle.addEventListener('click', openSidebar);
    menuToggle.addEventListener('click', resetSidebarTimer);
}

if (closeSidebar) {
    closeSidebar.addEventListener('click', closeSidebarFunc);
}

if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeSidebarFunc);
}

// Get all navigation links (both desktop and mobile)
const navLinks = document.querySelectorAll('.admin-nav-link');

// Function to set active link
function setActiveLink(clickedLink) {
    // Remove active class from ALL navigation links (both desktop and mobile)
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Add active class to the clicked link
    clickedLink.classList.add('active');
    
    // Also find and activate the corresponding link in the other sidebar
    // (if clicking mobile link, activate desktop link and vice versa)
    const href = clickedLink.getAttribute('href');
    if (href && href !== '#') {
        const correspondingLinks = document.querySelectorAll(`.admin-nav-link[href="${href}"]`);
        correspondingLinks.forEach(link => {
            link.classList.add('active');
        });
        
        // Save to localStorage for persistence across page reloads
        localStorage.setItem('activeNavLink', href);
    }
}

// Function to restore active state on page load
function restoreActiveLink() {
    const currentPage = window.location.pathname.split('/').pop();
    
    // Check if current page is dashboard (index.php)
    if (currentPage === 'index.php' || currentPage === '') {
        // If on dashboard, clear all active states and don't highlight anything
        navLinks.forEach(link => link.classList.remove('active'));
        // Don't update localStorage when on dashboard
        return;
    }
    
    // For other pages, always highlight the current page (not the saved one)
    // This ensures that when coming from dashboard boxes, the correct page is highlighted
    if (currentPage) {
        // Remove active from all links first
        navLinks.forEach(link => link.classList.remove('active'));
        
        // Find and activate links that match current page
        const currentPageLinks = document.querySelectorAll(`.admin-nav-link[href="${currentPage}"]`);
        if (currentPageLinks.length > 0) {
            currentPageLinks.forEach(link => link.classList.add('active'));
            localStorage.setItem('activeNavLink', currentPage);
        }
    }
}

// Add click event listeners to all navigation links
navLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Don't prevent default for actual page links, only for anchors
        if (href === '#' || href.startsWith('#')) {
            e.preventDefault();
        }
        
        // Handle dashboard link differently
        if (href === 'index.php') {
            // Remove active from all links when going to dashboard
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            // Don't save dashboard as active link
            // This allows returning to previous active state when coming back from dashboard
        } else {
            // For other pages, set as active and save
            setActiveLink(this);
        }
        
        // Close mobile sidebar after clicking a link (for mobile only)
        if (window.innerWidth < 768 && adminSidebar && adminSidebar.classList.contains('show')) {
            setTimeout(() => {
                closeSidebarFunc();
            }, 200); // Small delay for better UX
        }
    });
});

// Restore active link when page loads
document.addEventListener('DOMContentLoaded', function() {
    restoreActiveLink();
});

// Handle window resize - close mobile sidebar if screen becomes large
window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
        closeSidebarFunc();
    }
});

// Close sidebar with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && adminSidebar && adminSidebar.classList.contains('show')) {
        closeSidebarFunc();
    }
});

// Handle dashboard box clicks and logout
document.addEventListener('click', function(e) {
    const target = e.target.closest('a');
    if (target) {
        const href = target.getAttribute('href');
        
        // Clear localStorage when logging out
        if (href === 'login.php') {
            localStorage.removeItem('activeNavLink');
        }
        
        // Handle clicks on dashboard boxes (from dashboard page)
        if (target.classList.contains('dashboard-box')) {
            // Save the clicked page as active link
            localStorage.setItem('activeNavLink', href);
        }
    }
});

// Auto-close mobile sidebar after inactivity (optional)
let sidebarTimer;
function resetSidebarTimer() {
    clearTimeout(sidebarTimer);
    sidebarTimer = setTimeout(() => {
        if (window.innerWidth < 768 && adminSidebar && adminSidebar.classList.contains('show')) {
            closeSidebarFunc();
        }
    }, 30000); // Auto-close after 30 seconds
}

// Reset timer on user interaction
if (adminSidebar) {
    adminSidebar.addEventListener('mousemove', resetSidebarTimer);
    adminSidebar.addEventListener('touchstart', resetSidebarTimer);
}