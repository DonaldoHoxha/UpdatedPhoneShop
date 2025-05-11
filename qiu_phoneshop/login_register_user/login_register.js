document.addEventListener('DOMContentLoaded', () => {
    // Elements for panel switching
    const registerBefore = document.querySelector('.register_before');
    const signUpBox = document.querySelector('.signupBox');
    const loginAfter = document.querySelector('.login_after');
    const loginBox = document.querySelector('.loginBox');

    // Buttons for triggering panel switches
    const signupTrigger = document.querySelector('.signup-trigger');
    const loginTrigger = document.querySelector('.login-trigger');
    const mobileSignupTrigger = document.querySelector('.mobile-signup-trigger');
    const backArrow = document.querySelector('.back-arrow');

    // Apply smooth transitions
    document.querySelectorAll('.box').forEach(box => {
        box.style.transition = 'opacity 0.4s ease, visibility 0.4s ease';
    });

    // Show signup panel
    const showSignup = () => {
        registerBefore.classList.add('hidden');
        signUpBox.classList.remove('hidden');
        loginBox.classList.add('hidden');
        loginAfter.classList.remove('hidden');
    };

    // Show login panel
    const showLogin = () => {
        loginAfter.classList.add('hidden');
        loginBox.classList.remove('hidden');
        signUpBox.classList.add('hidden');
        registerBefore.classList.remove('hidden');
    };

    // Event listeners for desktop view
    signupTrigger.addEventListener('click', showSignup);
    loginTrigger.addEventListener('click', showLogin);

    // Event listener for mobile view signup
    if (mobileSignupTrigger) {
        mobileSignupTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            // Create mobile signup form
            loginBox.classList.add('hidden');
            signUpBox.classList.remove('hidden');
            signUpBox.style.position = 'relative';
        });
    }

    // Back arrow to return to login on mobile
    if (backArrow) {
        backArrow.addEventListener('click', () => {
            signUpBox.classList.add('hidden');
            loginBox.classList.remove('hidden');
        });
    }

    // Check for error parameters on page load
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        console.log(error);

        // Show signup form if there are signup errors
        if (error === 'email_exists' || error === 'username_exists') {
            showSignup();

            // Create error message
            const newP = document.createElement('p');
            newP.className = 'error';

            switch (error) {
                case 'email_exists':
                    const emailInput = document.getElementById('signupEmail');
                    newP.textContent = 'Email already exists';
                    emailInput.parentNode.insertAdjacentElement('afterend', newP);
                    emailInput.style.borderColor = '#e74c3c';
                    break;

                case 'username_exists':
                    const usernameInput = document.getElementById('signupUsername');
                    newP.textContent = 'Username already exists';
                    usernameInput.parentNode.insertAdjacentElement('afterend', newP);
                    usernameInput.style.borderColor = '#e74c3c';
                    break;
            }
        }
    }

    // Add input focus effects
    const formInputs = document.querySelectorAll(
        'input:not([type="checkbox"]):not([type="submit"])'
    );

    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            const icon = input.parentNode.querySelector('i');
            if (icon) { // Safety check
                icon.style.color = 'var(--secondary-color)';
            }
        });

        input.addEventListener('blur', () => {
            const icon = input.parentNode.querySelector('i');
            if (icon) { // Safety check
                icon.style.color = 'var(--dark-gray)';
            }
        });
    });
});