// Session persistence check for HTML pages
function checkSession() {
    // Check if user is logged in by making a request to a PHP file
    fetch('check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                // User is logged in, update navigation
                updateNavigationForLoggedInUser();
            } else {
                // User is not logged in, update navigation
                updateNavigationForLoggedOutUser();
            }
        })
        .catch(error => {
            console.log('Session check failed:', error);
            // Default to logged out state
            updateNavigationForLoggedOutUser();
        });
}

function updateNavigationForLoggedInUser() {
    // Update View Foods link to go to dashboard
    const viewFoodsLink = document.querySelector('a[href="signin.php"]');
    if (viewFoodsLink && viewFoodsLink.textContent.includes('View Foods')) {
        viewFoodsLink.href = 'unified_dashboard.php';
        viewFoodsLink.textContent = 'View Foods';
    }
    
    // Update Profile link to go to profile
    const profileLink = document.querySelector('a[href="profile.php"]');
    if (profileLink) {
        profileLink.href = 'profile.php';
    }
}

function updateNavigationForLoggedOutUser() {
    // Update View Foods link to go to signin
    const viewFoodsLink = document.querySelector('a[href="unified_dashboard.php"]');
    if (viewFoodsLink) {
        viewFoodsLink.href = 'signin.php';
        viewFoodsLink.textContent = 'View Foods';
    }
    
    // Update Profile link to go to signin
    const profileLink = document.querySelector('a[href="profile.php"]');
    if (profileLink) {
        profileLink.href = 'signin.php';
    }
}

// Run session check when page loads
document.addEventListener('DOMContentLoaded', checkSession);
