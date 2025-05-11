// Attende che il DOM sia completamente caricato
document.addEventListener('DOMContentLoaded', (event) => {
    // Seleziona l'elemento per il recupero password
    const forgot_psw = document.querySelector('.forgot_psw');

    // Seleziona gli elementi principali dell'interfaccia
    const registerBefore = document.querySelector('.register_before');
    const signUpBox = document.querySelector('.signupBox');
    const loginAfter = document.querySelector('.login_after');
    const loginBox = document.querySelector('.loginBox');
    
    // Aggiunge transizioni CSS per un effetto fluido
    signUpBox.style.transition = 'background-color 2s ease';
    loginBox.style.transition = 'background-color 2s ease';

    // 1. Gestione degli eventi hover per il toggle tra login e registrazione
    // ----------------------------------------------------------------
    registerBefore.addEventListener('mouseenter', () => {
        // Nasconde il pulsante "registrati" e mostra il form di registrazione
        registerBefore.classList.add('hidden');
        signUpBox.classList.remove('hidden');
        signUpBox.style.backgroundColor = '#f1f1f1';
        
        // Nasconde il form di login e mostra il pulsante "login"
        loginBox.classList.add('hidden');
        loginAfter.classList.remove('hidden');
        loginAfter.style.backgroundColor = "#ddd";
    });

    loginAfter.addEventListener('mouseenter', () => {
        // Nasconde il pulsante "login" e mostra il form di login
        loginAfter.classList.add('hidden');
        loginBox.classList.remove('hidden');
        
        // Nasconde il form di registrazione e mostra il pulsante "registrati"
        signUpBox.classList.add('hidden');
        registerBefore.classList.remove('hidden');
    });

    // 2. Gestione degli errori provenienti dal server
    // ----------------------------------------------------------------
    const urlParams = new URLSearchParams(window.location.search);
    const newP = document.createElement('p');
    newP.className = 'error';

    if (urlParams.has('error')) {
        const error = urlParams.get('error');
        console.log(error);
        
        // Gestione dei diversi tipi di errore
        switch (error) {
            case 'email_exists':
                const signupEmail = document.querySelector('.signupEmail');
                newP.textContent = 'Email already exists';
                signupEmail.appendChild(newP.cloneNode(true));
                break;
                
            case 'username_exists':
                const signupUsername = document.querySelector('.signupUsername');
                newP.textContent = 'Username already exists';
                signupUsername.appendChild(newP.cloneNode(true));
                break;
        }
    }

    // 3. Effetti visivi per gli input del form
    // ----------------------------------------------------------------
    const formInputs = document.querySelectorAll('input:not([type="checkbox"])');
    formInputs.forEach(input => {
        // Cambia colore dell'icona quando l'input è in focus
        input.addEventListener('focus', () => {
            input.parentNode.querySelector('i').style.color = 'var(--secondary-color)';
        });

        // Ripristina il colore originale quando perde il focus
        input.addEventListener('blur', () => {
            input.parentNode.querySelector('i').style.color = 'var(--dark-gray)';
        });
    });
});