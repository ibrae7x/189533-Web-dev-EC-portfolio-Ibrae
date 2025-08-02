/**
 * Ibrae Portfolio - Main JavaScript
 * Handles form submissions, authentication, and dynamic content
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all form handlers
    initContactForm();
    initSignInForm();
    initSignUpForm();
    checkUserSession();
});

/**
 * Contact Form Handler
 */
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        try {
            const formData = new FormData(contactForm);
            const data = {
                action: 'contact',
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                subject: formData.get('subject'),
                message: formData.get('message'),
                newsletter: formData.get('newsletter') === 'on'
            };

            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                showMessage('success', result.message);
                contactForm.reset();
            } else {
                showMessage('error', result.message);
            }
            
        } catch (error) {
            console.error('Contact form error:', error);
            showMessage('error', 'Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

/**
 * Sign In Form Handler
 */
function initSignInForm() {
    const signInForm = document.getElementById('signInForm');
    if (!signInForm) return;

    signInForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = signInForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing in...';
        
        try {
            const formData = new FormData(signInForm);
            const data = {
                action: 'signin',
                email: formData.get('email'),
                password: formData.get('password')
            };

            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                showMessage('success', result.message);
                // Redirect after successful login
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1500);
            } else {
                showMessage('error', result.message);
            }
            
        } catch (error) {
            console.error('Sign in error:', error);
            showMessage('error', 'Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

/**
 * Sign Up Form Handler
 */
function initSignUpForm() {
    const signUpForm = document.getElementById('signUpForm');
    console.log('Looking for signUpForm:', signUpForm);
    if (!signUpForm) {
        console.log('signUpForm not found!');
        return;
    }
    
    console.log('signUpForm found, attaching event listener...');

    signUpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('signUpForm submitted!');
        
        const submitBtn = signUpForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creating account...';
        
        try {
            const formData = new FormData(signUpForm);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirmPassword');
            const terms = formData.get('terms'); // Get terms checkbox
            
            // Validation
            if (password !== confirmPassword) {
                showMessage('error', 'Passwords do not match');
                return;
            }
            
            if (!terms) {
                showMessage('error', 'Please accept the terms and conditions');
                return;
            }
            
            if (password.length < 6) {
                showMessage('error', 'Password should be at least 6 characters long');
                return;
            }
            
            const data = {
                action: 'signup',
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: password,
                confirmPassword: confirmPassword
            };

            console.log('Sending signup data:', data);

            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Signup API response:', result);
            
            if (result.success) {
                console.log('Signup successful!');
                showMessage('success', result.message);
                // Redirect to sign in page after successful registration
                setTimeout(() => {
                    window.location.href = 'signin.html';
                }, 2000);
            } else {
                showMessage('error', result.message);
            }
            
        } catch (error) {
            console.error('Sign up error:', error);
            showMessage('error', 'Network error. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

/**
 * Check user session and update navbar
 */
function checkUserSession() {
    // This would typically check if user is logged in
    // For now, we'll check localStorage for demo purposes
    const userInfo = localStorage.getItem('userInfo');
    if (userInfo) {
        const user = JSON.parse(userInfo);
        updateNavbarForLoggedInUser(user);
    }
}

/**
 * Update navbar for logged in user
 */
function updateNavbarForLoggedInUser(user) {
    const authLinks = document.querySelector('.auth-links');
    if (authLinks && user) {
        authLinks.innerHTML = `
            <span class="navbar-text me-3">Welcome, ${user.firstName}!</span>
            <button class="btn btn-outline-light btn-sm" onclick="signOut()">Sign Out</button>
        `;
    }
}

/**
 * Sign out function
 */
function signOut() {
    localStorage.removeItem('userInfo');
    window.location.href = 'index.html';
}

/**
 * Show message to user
 */
function showMessage(type, message, targetForm = null) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert with better styling and positioning
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? '✅' : '❌';
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow-sm" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050; max-width: 400px; border-radius: 8px;">
            <strong>${icon}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at the end of body for fixed positioning
    document.body.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-remove success messages after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
}

/**
 * Utility function to get CSRF token (if needed)
 */
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : null;
}

// Smooth scrolling for anchor links
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

console.log('🎯 Ibrae Portfolio JavaScript loaded successfully!');
